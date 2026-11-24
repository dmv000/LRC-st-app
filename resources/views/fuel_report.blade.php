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
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>{{ $PageTitle }}</h3>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Fuel Report From {{ $StartDate }} till {{ $EndDate }}</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <fieldset>
                        <div class="control-group">
                            <div class="x_content">
                                <table id="example" class="display" width="100%">
				<tfoot>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					</tfoot>
				</table>
                            </div>
                        </div>
                    </fieldset>
                    <div class="clearfix"></div>
                </div>
                <div class="x_title">
                    <h2>Change Search Parameters</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <fieldset>
                        <div class="control-group">
                            <div class="x_content">
                                <table>
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Date Range / التاريخ :&nbsp;</label>
                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                     <form class="form-horizontal">
                                                        <fieldset>
                                                            <div class="control-group">
                                                                <div class="controls">
                                                                    <div class="input-prepend input-group">
                                                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                                        <input type="text" style="width: 200px" name="reservation" id="reservation" class="form-control" value="{{ $StartDate }} - {{ $EndDate }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </fieldset>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                        <td>                                            
                                            <div class="form-group">
                                                <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >&nbsp;</label>
                                                <div class="col-md-9 col-sm-9 col-xs-12">
                                                    <button style="margin-bottom:10px" onclick="changePage()" type='button' class='btn btn-primary btn-min-width'>Search</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="error_td">
                                            <span id="start_date_error" class="error"></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </fieldset>
                    <div class="clearfix"></div>
                </div>       
            </div>
        </div>
    </div>
</div>


<script src="../build/js/jquery.dataTables.min.js"></script>
<script>

    function changePage(){

        var date = $('#reservation').val().trim();
        var StartDate = date.split('-')[0].trim();
        var EndDate = date.split('-')[1].trim();

        if (Date.parse(EndDate) < Date.parse(StartDate)) {
            $("#start_date_error").text('Start Date must be lower than End Date');
        }else{
                window.location = "/driver_fuel_logs?StartDate=" + StartDate + "&EndDate=" + EndDate;
        }
        
    }

    var dataSet = [];

   dataSet = [@forelse($logs as $log)
            [
	"{{$log['id']}}",
	"",
	"{{$log['date']}}",
	"{{$log['driver']}}",
	"{{$log['ambulance']}}",
	"{{$log['kms']}}",
	"{{$log['coupon_number']}}",
	"{{$log['litre']}}",
	"{{$log['price']}}",
	"{{$log['station']}}",
	"{{$log['type']}}"
            ],
	@empty
        @endforelse
        ];
$(document).ready(function() {

filt(table);


});

function filt(table){

table.columns([3,4]).every( function () {
                var column = this;
                var select = $('<div class="col-md-9 col-sm-9 col-xs-12" style="padding:0px;"><select class="form-control"><option value=""></option></select></div>')
                    .appendTo( $(column.footer()).empty() );

	select.find('select').on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.find('select').append( '<option value="'+d+'">'+d+'</option>' )
                } );

});
}



        var table = $('#example').DataTable( {
            data: dataSet,
	    "order": [[ 2, "desc" ]],
            columns: [
                { title : "Id" },
	{ title : "" },
                { title: "Date" },
                { title: "Driver",width:"20%" },
                { title: "Ambulance" ,width:"20%" },
                { title: "KM" },
                { title: "Number" },
                { title: "Litres" },
                { title: "Price" },
                { title: "Station" },
                { title: "Type" }
            ],
            "searching": true,
            "lengthChange": false,
            "pageLength": 15,
            "scrollX": true,
            "columnDefs": [ {
                "targets": 1,
                "data": null,
                "sortable": false,
                "defaultContent": "<button type='button' class='btn btn-info btn-min-width'>Edit</button>@if(Auth::check() && (Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Drivers Admin')->first()->id)
			||
		Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Head of Station')->first()->id))) &nbsp;<button type='button' class='btn btn-info btn-min-width delete'>Delete</button> @endif"
            },
            {
                "targets" : 0,
                "visible" : false,
                "searchable": false
            }
             ]
            });
    
    $('#example tbody').on('click', 'button', function () {
 
        var table = $('#example').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
	if(!$(this).hasClass('delete')) {
        window.location = "/edit_fuel_coupon?Id=" + data[0];
	}else{
	delete_coupon(data[0]);
//	window.location = "/edit_fuel_coupon?Id=" + data[0];
	}
        
    });

    $('#example tbody')
        .on( 'mouseenter', 'td', function () {
            var table = $('#example').DataTable();
            var colIdx = table.cell(this).index().column;
            $( table.cells().nodes() ).removeClass( 'highlight' );
            $( table.column( colIdx ).nodes() ).addClass( 'highlight' );
        } 
    );

function delete_coupon(id){
        var formData = {
            id :id,
            "_token": "{{ csrf_token() }}"                        
        }

        $.ajax({
            url:'delete_fuel_coupon',
            dataType:'json',
            type: 'post',
            data: formData,
            success:function(response){
                if(response.Status == 1){
                    alert("Fuel Coupon Removed Succesfully");
                    location.reload();
                }else {
		    alert(response.Message);
		}
            }
        });
}



    
</script>
@endsection
