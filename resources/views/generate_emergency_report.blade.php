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
<link href="css/select2.css" rel="stylesheet" />
<script src="js/select2.js"></script>

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
					<h2>EMT Count</h2>
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
																		@for($i = 2018; $i <= $year + 1; $i++)	
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
												<button style="margin-bottom:10px" onclick="getEmtCountReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
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
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Support</h2>
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
																	<select class="form-control standardSelect" name="year" id="year2" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Year...">
																		<option value="-1"></option>
																		@for($i = 2018; $i <= $year + 1; $i++)	
																		<option @if($i == $year) selected @endif value=" {{ $i }} ">{{ $i }}</option>
																		@endfor
																	</select>
																</div>
																<div class="input-prepend input-group" style="width: 400px;">
																	<?php $month = date('m'); ?>
																	<select class="form-control standardSelect" name="month" id="month2" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Month...">
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
												<button style="margin-bottom:10px" onclick="getSupportReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
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
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Day Team Missions</h2>
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
																	<select class="form-control standardSelect" name="year" id="yearDTM" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Year...">
																		<option value="-1"></option>
																		@for($i = 2018; $i <= $year + 1; $i++)	
																		<option @if($i == $year) selected @endif value=" {{ $i }} ">{{ $i }}</option>
																		@endfor
																	</select>
																</div>
																<div class="input-prepend input-group" style="width: 400px;">
																	<?php $month = date('m'); ?>
																	<select class="form-control standardSelect" name="month" id="monthDTM" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Month...">
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
												<button style="margin-bottom:10px" onclick="getDTMReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
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
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Village Report</h2>
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
																	<select class="form-control standardSelect" name="year" id="year3" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Year...">
																		<option value="-1"></option>
																		@for($i = 2018; $i <= $year + 1; $i++)	
																		<option @if($i == $year) selected @endif value=" {{ $i }} ">{{ $i }}</option>
																		@endfor
																	</select>
																</div>
																<div class="input-prepend input-group" style="width: 400px;">
																	<?php $month = date('m'); ?>
																	<select class="form-control standardSelect" name="month" id="month3" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Month...">
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
												<button style="margin-bottom:10px" onclick="getVillageReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
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
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Mission Type Report</h2>
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
																	<select class="form-control standardSelect" name="year" id="year4" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Year...">
																		<option value="-1"></option>
																		@for($i = 2018; $i <= $year + 1; $i++)	
																		<option @if($i == $year) selected @endif value=" {{ $i }} ">{{ $i }}</option>
																		@endfor
																	</select>
																</div>
																<div class="input-prepend input-group" style="width: 400px;">
																	<?php $month = date('m'); ?>
																	<select class="form-control standardSelect" name="month" id="month4" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Month...">
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
												<button style="margin-bottom:10px" onclick="getMissionTypeReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
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
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Times</h2>
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
																	<select class="form-control standardSelect" name="year" id="year5" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Year...">
																		<option value="-1"></option>
																		@for($i = 2018; $i <= $year + 1; $i++)	
																		<option @if($i == $year) selected @endif value=" {{ $i }} ">{{ $i }}</option>
																		@endfor
																	</select>
																</div>
																<div class="input-prepend input-group" style="width: 400px;">
																	<?php $month = date('m'); ?>
																	<select class="form-control standardSelect" name="month" id="month5" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Month...">
																		<option value="-1"></option>
																		@for($i = 1; $i <= 12; $i++)	
																		<option @if($i == $month - 1) selected @endif value=" {{ $i }} ">{{ date("F", mktime(0, 0, 0, $i, 10)) }}</option>
																		@endfor
																	</select>
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
											<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;">&nbsp;</label>
											<div class="col-md-9 col-sm-9 col-sx-12">
												<button style="margin-bottom:10px" onclick="getTimesReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
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
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Ambulance</h2>
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
																	<select class="form-control standardSelect" name="year" id="yearAmbulance" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Year...">
																		<option value="-1"></option>
																		@for($i = 2018; $i <= $year + 1; $i++)	
																		<option @if($i == $year) selected @endif value=" {{ $i }} ">{{ $i }}</option>
																		@endfor
																	</select>
																</div>
																<div class="input-prepend input-group" style="width: 400px;">
																	<?php $month = date('m'); ?>
																	<select class="form-control standardSelect" name="month" id="monthAmbulance" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Month...">
																		<option value="-1"></option>
																		@for($i = 1; $i <= 12; $i++)	
																		<option @if($i == $month - 1) selected @endif value=" {{ $i }} ">{{ date("F", mktime(0, 0, 0, $i, 10)) }}</option>
																		@endfor
																	</select>
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
											<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;">&nbsp;</label>
											<div class="col-md-9 col-sm-9 col-sx-12">
												<button style="margin-bottom:10px" onclick="getAmbulanceReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
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
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Infecteous Disease Report</h2>
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
																	<select class="form-control standardSelect" name="year" id="yearInf" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Year...">
																		<option value="-1"></option>
																		@for($i = 2018; $i <= $year + 1; $i++)	
																		<option @if($i == $year) selected @endif value=" {{ $i }} ">{{ $i }}</option>
																		@endfor
																	</select>
																</div>
																<div class="input-prepend input-group" style="width: 400px;">
																	<?php $month = date('m'); ?>
																	<select class="form-control standardSelect" name="month" id="monthInf" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Month...">
																		<option value="-1"></option>
																		@for($i = 1; $i <= 12; $i++)	
																		<option @if($i == $month - 1) selected @endif value=" {{ $i }} ">{{ date("F", mktime(0, 0, 0, $i, 10)) }}</option>
																		@endfor
																	</select>
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
											<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;">&nbsp;</label>
											<div class="col-md-9 col-sm-9 col-sx-12">
												<button style="margin-bottom:10px" onclick="getInfecteousReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
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
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Detailed Report</h2>
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
																	<select class="form-control standardSelect" name="year" id="yearDet" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Year...">
																		<option value="-1"></option>
																		@for($i = 2018; $i <= $year + 1; $i++)	
																		<option @if($i == $year) selected @endif value=" {{ $i }} ">{{ $i }}</option>
																		@endfor
																	</select>
																</div>
																<div class="input-prepend input-group" style="width: 400px;">
																	<?php $month = date('m'); ?>
																	<select class="form-control standardSelect" name="month" id="monthDet" style="display:none; min-width:400px;" tabindex="-1" data-placeholder="Choose a Month...">
																		<option value="-1"></option>
																		@for($i = 1; $i <= 12; $i++)	
																		<option @if($i == $month - 1) selected @endif value=" {{ $i }} ">{{ date("F", mktime(0, 0, 0, $i, 10)) }}</option>
																		@endfor
																	</select>
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
											<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;">&nbsp;</label>
											<div class="col-md-9 col-sm-9 col-sx-12">
												<button style="margin-bottom:10px" onclick="getDetailedReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
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
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Salle de Soins</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
					</ul>
					<div class="clearfix">
					</div>
				</div>
				<div class="x_content">
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
														<div class="controls">
															<div class="input-prepend input-group" style="width: 400px">
																<span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
																<input type="text" name="reservation" id="reservation" class="form-control">
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
										<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;">&nbsp;</label>
										<div class="col-md-9 col-sm-9 col-sx-12">
											<button style="margin-bottom:10px" onclick="getFirstAidReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
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
					<div class="clearfix"></div>
				</div>   
			</div>
		</div>
	</div>
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Badal Report</h2>
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
															<div class="controls">
																<div class="input-prepend input-group" style="width: 400px">
																	<span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
																	<input type="text" name="reservation2" id="reservation2" class="form-control">
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
											<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;">&nbsp;</label>
											<div class="col-md-9 col-sm-9 col-sx-12">
												<button style="margin-bottom:10px" onclick="getBadalReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td class="error-td">
										<span id="start_date_error2" class="error"></span>
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
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Role Breakdown Report</h2>
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
															<div class="controls">
																<div class="input-prepend input-group" style="width: 400px">
																	<span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
																	<input type="text" name="reservation3" id="reservation3" class="form-control">
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
											<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;">&nbsp;</label>
											<div class="col-md-9 col-sm-9 col-sx-12">
												<button style="margin-bottom:10px" onclick="getRoleReport()" type="button" class="btn btn-primary btn-min-width">Get Report</button>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td class="error-td">
										<span id="start_date_error3" class="error"></span>
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

    function getRoleReport(){
		
		var date = $('#reservation3').val().trim();
        var StartDate = date.split('-')[0].trim();
        var EndDate = date.split('-')[1].trim();

        if (Date.parse(EndDate) < Date.parse(StartDate)) {
            $("#start_date_error3").text('Start Date must be lower than End Date');
        }else{
            window.open(
				"/role_report?startDate=" + StartDate + "&endDate=" + EndDate,
				'_blank'
			);
        }
    }

    function getBadalReport(){
		
		var date = $('#reservation2').val().trim();
        var StartDate = date.split('-')[0].trim();
        var EndDate = date.split('-')[1].trim();

        var year = $('#year6').val();
		var month = $('#month6').val();

        if (Date.parse(EndDate) < Date.parse(StartDate)) {
            $("#start_date_error2").text('Start Date must be lower than End Date');
        }else{
            window.open(
				"/emt_support_report?startDate=" + StartDate + "&endDate=" + EndDate,
				'_blank'
			);
        }
    }

    function getFirstAidReport(){

        var date = $('#reservation').val().trim();
        var StartDate = date.split('-')[0].trim();
        var EndDate = date.split('-')[1].trim();

        if (Date.parse(EndDate) < Date.parse(StartDate)) {
            $("#start_date_error").text('Start Date must be lower than End Date');
        }else{
		window.open(
			"/first_aid_report?StartDate=" + StartDate + "&EndDate=" + EndDate,
			'_blank'
		);
        }
    }

    function getEmtCountReport(){

        var year = $('#year').val();
	var month = $('#month').val();

        if (year != "" && year != -1 && month != "" && month != -1 ) {
            //$("#start_date_error").text('Start Date must be lower than End Date');
		window.open(
			"/emt_count?year=" + year + "&month=" + month,
			'_blank'
		);
        }//else{
	//	window.open(
	//		"/first_aid_report?StartDate=" + StartDate + "&EndDate=" + EndDate,
	//		'_blank'
	//	);
        //}
    }

    function getVillageReport(){

        var year = $('#year3').val();
	var month = $('#month3').val();

        if (year != "" && year != -1 && month != "" && month != -1 ) {
            //$("#start_date_error").text('Start Date must be lower than End Date');
		window.open(
			"/village_report?year=" + year + "&month=" + month,
			'_blank'
		);
        }//else{
	//	window.open(
	//		"/first_aid_report?StartDate=" + StartDate + "&EndDate=" + EndDate,
	//		'_blank'
	//	);
        //}
    }
	
	function getAmbulanceReport(){

        var year = $('#yearAmbulance').val();
		var month = $('#monthAmbulance').val();

        if (year != "" && year != -1 && month != "" && month != -1 ) {
            //$("#start_date_error").text('Start Date must be lower than End Date');
			window.open("/ambulance_report?year=" + year + "&month=" + month,'_blank');
        }
    }
	
	function getInfecteousReport(){

        var year = $('#yearInf').val();
		var month = $('#monthInf').val();

        if (year != "" && year != -1 && month != "" && month != -1 ) {
            //$("#start_date_error").text('Start Date must be lower than End Date');
			window.open("/infecteous_disease_report?year=" + year + "&month=" + month,'_blank');
        }
    }
	
	function getDetailedReport(){

        var year = $('#yearDet').val();
		var month = $('#monthDet').val();

        if (year != "" && year != -1 && month != "" && month != -1 ) {
            //$("#start_date_error").text('Start Date must be lower than End Date');
			window.open("/detailed_report?year=" + year + "&month=" + month,'_blank');
        }
    }

    function getTimesReport(){

        var year = $('#year5').val();
	var month = $('#month5').val();

        if (year != "" && year != -1 && month != "" && month != -1 ) {
            //$("#start_date_error").text('Start Date must be lower than End Date');
		window.open(
			"/times_report?year=" + year + "&month=" + month,
			'_blank'
		);
        }//else{
	//	window.open(
	//		"/first_aid_report?StartDate=" + StartDate + "&EndDate=" + EndDate,
	//		'_blank'
	//	);
        //}
    }

    function getSupportReport(){

        var year = $('#year2').val();
	var month = $('#month2').val();

        if (year != "" && year != -1 && month != "" && month != -1 ) {
            //$("#start_date_error").text('Start Date must be lower than End Date');
		window.open(
			"/support_report?year=" + year + "&month=" + month,
			'_blank'
		);
        }//else{
	//	window.open(
	//		"/first_aid_report?StartDate=" + StartDate + "&EndDate=" + EndDate,
	//		'_blank'
	//	);
        //}
    }

    function getMissionTypeReport(){

        var year = $('#year4').val();
	var month = $('#month4').val();

        if (year != "" && year != -1 && month != "" && month != -1 ) {
            //$("#start_date_error").text('Start Date must be lower than End Date');
		window.open(
			"/mission_type_report?year=" + year + "&month=" + month,
			'_blank'
		);
        }//else{
	//	window.open(
	//		"/first_aid_report?StartDate=" + StartDate + "&EndDate=" + EndDate,
	//		'_blank'
	//	);
        //}
    }

    function getDTMReport(){

        var year = $('#yearDTM').val();
	var month = $('#monthDTM').val();

        if (year != "" && year != -1 && month != "" && month != -1 ) {
            //$("#start_date_error").text('Start Date must be lower than End Date');
		window.open(
			"/day_team_report?year=" + year + "&month=" + month,
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

		$(".standardSelect").select2({ width : "100%" });
//        	$(".standardSelect").chosen({
//           		disable_search_threshold: 10,
//            		no_results_text: "Oops, nothing found!",
//            		width: "100%",
//            		disabled: true
//        	});
    	});
		
		$('#reservation2').daterangepicker({drops:'up'});
		$('#reservation3').daterangepicker({drops:'up'});
});
</script>
@endsection
