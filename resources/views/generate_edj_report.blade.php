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
			<h3>{{ $page_title }}</h3>
		</div>
	</div>
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Equipe de Jour Shifts</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
					</ul>
					<div class="clearfix">
					</div>
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
																	<?php $year = date('Y'); ?>
																	<select class="form-control standardSelect" name="year" id="year" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Year...">
																		<option value="-1"></option>
																		@for($i = $year - 1; $i <= $year + 1; $i++)	
																		<option @if($i == $year) selected @endif value=" {{ $i }} ">{{ $i }}</option>
																		@endfor
																	</select>
																</div>
																<div class="input-prepend input-group" style="width: 400px;">
																	<?php $month = date('m'); ?>
																	<select class="form-control standardSelect" name="month" id="month" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Month...">
																		<option value="-1"></option>
																		@for($i = 1; $i <= 12; $i++)	
																		<option @if($i == $month - 1) selected @endif value=" {{ $i }} ">{{ date("F", mktime(0, 0, 0, $i, 10)) }}</option>
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
												<button style="margin-bottom:10px" onclick="getEdjCountReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
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
				</div>   
			</div>
		</div>
	</div>
</div>

<script>

    function getEdjCountReport(){

        var year = $('#year').val();
	var month = $('#month').val();

        if (year != "" && year != -1 && month != "" && month != -1 ) {
            //$("#start_date_error").text('Start Date must be lower than End Date');
		window.open(
			"/edj_shifts_report?year=" + year + "&month=" + month,
			'_blank'
		);
        }//else{
	//	window.open(
	//		"/first_aid_report?StartDate=" + StartDate + "&EndDate=" + EndDate,
	//		'_blank'
	//	);
        //}
    }

$(document).ready(function() {


    	$(document).ready(function(){
        	$(".standardSelect").chosen({
            		disable_search_threshold: 10,
            		no_results_text: "Oops, nothing found!",
            		width: "100%",
            		disabled: true
        	});
    	});
});
</script>
@endsection
