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
                    <h2>Registers&nbsp;<span style="color:#FF6347">Not Finished</span>&nbsp;<span style="color:#169F85">Finished</span></h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
			<fieldset>
						<div class="control-group">
							<table>
								<tr>
									<td>
										<div class="form-group">
											<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Date Range&nbsp;</label>
											<div class="col-md-9 col-sm-9 col-xs-12">
												<form class="form-horizontal">
													<fieldset>
														<div class="control-group">
															<div class="controls" style="width: 400px;">
																<div class="input-prepend input-group" style="width: 400px;">
																	<select class="form-control standardSelect" name="year" id="year" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Year...">
																		<option value="-1"></option>
																		@for($i = $year - 1; $i <= $year + 1; $i++)	
																		<option @if($i == $year) selected @endif value=" {{ $i }} ">{{ $i }}</option>
																		@endfor
																	</select>
																</div>
																<div class="input-prepend input-group" style="width: 400px;">

																	<select class="form-control standardSelect" name="month" id="month" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Month...">
																		<option value="-1"></option>
																		@for($i = 1; $i <= 12; $i++)	
																		<option @if($i == $month) selected @endif value=" {{ $i }} ">{{ date("F", mktime(0, 0, 0, $i, 10)) }}</option>
																		@endfor
																	</select>
																</div
															</div>
														</div>
													</fieldset>
												</form>
											</div>
										</div>
									</td>
									<td>
										<div class="form-group">
											<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;">&nbsp;</label>
											<div class="col-md-9 col-sm-9 col-sx-12">
												<button style="margin-bottom:10px" onclick="go()" type="button" class="btn btn-primary btn-min-width">Get Registers</button>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td class="error-td">
										<span id="start_date_error" class="error"></span>
									</td>
								</tr>
							</table>
						</div>
					</fieldset>
					<div class="clearfix"></div>
<hr>
<br/>
                	<div id='calendar'></div>	
		<div>
		
	    </div>
	</div>
    </div>
</div>
<script src="vendors/fullcalendar/dist/fullcalendar.min.js"></script>
<script src="vendors/iCheck/icheck.min.js"></script>
<script>
$(function() {


$(".standardSelect").select2({ width : "100%" });

  // page is now ready, initialize the calendar...

	$('#calendar').fullCalendar({
		header: {
			left: '',
			center: 'title',
			right: ''
		},
		editable: false,
		weekends: true,
		displayEventTime: false
	});

var month = {{ $month }};
var year = {{ $year }};

$('#calendar').fullCalendar('gotoDate', new Date(year, month - 1));

@foreach($registers as $register) 
$("#calendar").fullCalendar('renderEvent', {
        title: '',
        start: new Date({{ $register->year }}, {{ $register->month }} - 1,{{ $register->day }}, 0,0,0),
	end: new Date({{ $register->year }}, {{ $register->month }}  - 1,{{ $register->day }}, 23 , 59 , 59),
	className: 'am',
	url : 'register?register_id={{ $register->id }}',
        stick: true,
	allDay : true,
	@if($register->finished == 1 ) color: '#169F85' @else color : '#FF6347' @endif
      });
@endforeach

});

function go() {

        var year = $('#year').val().trim();
	var month = $('#month').val().trim();

        if (year != "" && year != -1 && month != "" && month != -1 ) {
		window.open(
			"/all_register?year=" + year + "&month=" + month
		);
        }
};


</script>
@endsection
