@extends('layouts.master')
@section('content')

<style>
    .error{
        color:red;
        font-family: Cambira;
    }

    .error_td{
        padding-left:10px;
    }

    .error_h2{
        color:red;
    }
</style>
    <link href="vendors/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
        <link href="vendors/iCheck/skins/flat/green.css" rel="stylesheet">


<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>{{ $page_title }}</h3>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Equipe de Jour</h2>
                    <ul class="nav navbar-right panel_toolbox">
			<li>
				<label>
					<input type="checkbox" class="flat" id="am"> All AM Shifts &nbsp;
				</label>
			</li>
			<li>
				<label>
					<input type="checkbox" class="flat" id="pm"> All PM Shifts &nbsp;
				</label>
			</li>
			<li>
				<label>
					<input type="checkbox" class="flat" id="all"> All Shifts &nbsp;
				</label>
			</li>
			<!--<li>
				<label>
					<input type="checkbox" class="flat" id="cam"> All Technical AM Shifts &nbsp;
				</label>
			</li>
			<li>
				<label>
					<input type="checkbox" class="flat" id="cpm"> All Technical PM Shifts &nbsp;
				</label>
			</li>
			<li>
				<label>
					<input type="checkbox" class="flat" id="call"> All Technical Shifts &nbsp;
				</label>
			</li>-->
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
			
                	<div id='calendar'></div>
		<div>
		<hr>
 		<button type="button" class="btn btn-success btn-lg" onclick="reset()">Reset</button>
		<button type="button" class="btn btn-success btn-lg" onclick="submit()">Submit</button>
	    </div>
	</div>
    </div>
</div>
<script src="vendors/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="vendors/iCheck/icheck.min.js"></script>
<script>
$(function() {
  // page is now ready, initialize the calendar...

	$('#calendar').fullCalendar({
		header: {
			left: '',
			center: 'title',
			right: ''
		},
		editable: false,
		hiddenDays: [0],
		weekends: true,
	displayEventTime: false,
eventAfterRender:function( event, element, view ) { 
$(element).attr("id","event_id_"+event._id);
}
	})

var month = {{ $month }};
var year = {{ $year }};

$('#calendar').fullCalendar('gotoDate', new Date(year, month - 1));

var days = daysInMonth(month,year);

for (var day = 1; day <= days; day ++) {


$("#calendar").fullCalendar('renderEvent', {
	id : 'AM-' + day ,
        title: 'AM Shift',
        start: new Date(year,month - 1,day, 7,0,0),
	end: new Date(year,month - 1,day, 12,30,0),
	className: 'am',
        stick: true,
	color: '#169F85'
      });
$("#calendar").fullCalendar('renderEvent', {
	id : 'PM-' + day ,
        title: 'PM Shift',
        start: new Date(year,month - 1,day, 12,30,0),
	end: new Date(year,month - 1,day, 18,0,0),
	calssName: 'pm',
        stick: true,
	color: '#169F85'
      });

/*$("#calendar").fullCalendar('renderEvent', {
	id : 'CAM-' + day ,
        title: 'Covid AM Shift',
        start: new Date(year,month - 1,day, 7,0,0),
	end: new Date(year,month - 1,day, 12,30,0),
	className: 'cam',
        stick: true,
	color: '#a1b6ba'
      });
$("#calendar").fullCalendar('renderEvent', {
	id : 'CPM-' + day ,
        title: 'Covid PM Shift',
        start: new Date(year,month - 1,day, 12,30,0),
	end: new Date(year,month - 1,day, 18,0,0),
	calssName: 'cpm',
        stick: true,
	color: '#a1b6ba'
      });*/
	  
}

$('.fc-content').each( function () {

	if( $ ( this ).first( 'span' ).text() == " AM Shift" ) {

		$( this ).append( '<input type="checkbox" style="float:right; height:20px; width:20px" class="AM-Check Check">' );
	}else if( $ ( this ).first( 'span' ).text() == " PM Shift" ) {

		$( this ).append( '<input type="checkbox" style="float:right; height:20px; width:20px" class="PM-Check Check">' );
	}else if( $ ( this ).first( 'span' ).text() == " Covid AM Shift" ) {

		$( this ).append( '<input type="checkbox" style="float:right; height:20px; width:20px" class="CAM-Check Check">' );
	}else if( $ ( this ).first( 'span' ).text() == " Covid PM Shift" ) {

		$( this ).append( '<input type="checkbox" style="float:right; height:20px; width:20px" class="CPM-Check Check">' );
	}
});

@foreach($user_events as $event)
	$('#{{ $event }}').find('input').prop('checked',true);
@endforeach

});

function daysInMonth (month, year) { // Use 1 for January, 2 for February, etc.
	return new Date(year, month, 0).getDate();
}

$('#am').on('click', function(event){

	allShifts( $( this ) , 1);    
});

$('#pm').on('click', function(event){

	allShifts( $( this ) , 2);    
});

$('#all').on('click', function(event){

	allShifts( $( this ) , 3);    
});

$('#cam').on('click', function(event){

	allShifts( $( this ) , 4);    
});

$('#cpm').on('click', function(event){

	allShifts( $( this ) , 5);    
});

$('#call').on('click', function(event){

	allShifts( $( this ) , 6);    
});


function allShifts(e,type) {

	var check = $( e ).is(':checked');
	if(type == 1) {

		//AM Shifts
		$('.AM-Check').prop('checked',check);
	}else if (type == 2) {

		//PM Shifts
		$('.PM-Check').prop('checked',check);
	}else if (type == 3) {

		//All Shifts
		$('.AM-Check').prop('checked',check);
		$('.PM-Check').prop('checked',check);
	} else if(type == 4) {

		//AM Shifts
		$('.CAM-Check').prop('checked',check);
	}else if (type == 5) {

		//PM Shifts
		$('.CPM-Check').prop('checked',check);
	}else if (type == 6) {

		//All Shifts
		$('.CAM-Check').prop('checked',check);
		$('.CPM-Check').prop('checked',check);
	}
}

function reset() {
	$('.Check').prop('checked',false);
	$('#am').iCheck('uncheck');
	$('#pm').iCheck('uncheck');
	$('#all').iCheck('uncheck');
	$('#cam').iCheck('uncheck');
	$('#cpm').iCheck('uncheck');
	$('#call').iCheck('uncheck');
}

function submit() {
var month = {{ $month }};
var year = {{ $year }};

	var arr = [];

	$('.fc-content').each( function() {

		var input = $( this ).find( '.Check' );
		var check = $( this ).find( '.Check' ).is( ':checked' );
		if( $( this ).find( '.Check' ).is( ':checked' ) ) {

			arr.push({
				value : $( this ).parent().attr( 'id' )
			});
		}
	});

	/*var subForm = {};
	subForm['year'] = year;
	subForm['month'] = month;

	subForm['shifts'] = */
	var formData = {
		"_token": "{{ csrf_token() }}",
		"form" : JSON.stringify({
			year : year,
			month : month,
			shifts : arr
		})
    	}

           
    $.ajax({
        url:'add_edj_shifts',
        dataType:'json',
        type: 'post',
        data: formData,
        success:function(response){
            if(response.Status == 1){
                alert("Shifts Added Succesfully");
                //window.location.href="driver_fuel_logs";
            }else{
                alert(response.Message);
            }
        }
    });



}
</script>
@endsection
