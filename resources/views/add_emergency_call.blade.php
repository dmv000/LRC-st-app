@extends('layouts.master')
@section('content')

<link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">
<link href="../vendors/datetimepicker/datetimepicker.min.css" rel="stylesheet">
<link href="../vendors/switchery/dist/switchery.min.css" rel="stylesheet">

<style>
.disabled-div{
	background-color:#eee;
	pointer-events:none;
	cursor:not-allowed;
}
</style>

<div class="right_col" role="main">
	<div class="">
		<div class="page-title">
			<div class="title_left">
				<h3>Emergency In Progress</h3>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="col-md-12 col-sm-12 ">
			<div class="x_panel">
				<div class="x_title">
					<h2>Ambulance Progress</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
						</li>
					</ul>
					<div class="clearfix"></div>
				</div>
				<div class="x_content">
					<div id="wizard" class="form_wizard wizard_horizontal">
						<ul class="wizard_steps">
							<li>
								<a href="#step-1">
									<span class="step_no">1</span>
									<span class="step_descr">
										Call Received<br />
										<small>الاتصال</small><br />
										<small id="time-step1"></small><br />
									</span>
									<p id="amb-step1" class="fa fa-ambulance" style="font-size:48px;-webkit-transform: scaleX(-1);transform: scaleX(-1);color:red;"></p>
								</a>
							</li>
							<li>
								<a href="#step-2">
									<span class="step_no">2</span>
									<span class="step_descr">
										Departure to Site<br />
										<small>الانطلاق إلى الموقع</small><br />
										<small id="time-step2"></small><br />
									</span>
									<p id="amb-step2" class="fa fa-ambulance" style="font-size:48px;-webkit-transform: scaleX(-1);transform: scaleX(-1);color:red;display:none;"></p>
								</a>
							</li>
							<li>
								<a href="#step-3">
									<span class="step_no">3</span>
									<span class="step_descr">
										Arrival on Site<br />
										<small>الوصول إلى الموقع</small><br />
										<small id="time-step3"></small><br />
									</span>
									<p id="amb-step3" class="fa fa-ambulance" style="font-size:48px;-webkit-transform: scaleX(-1);transform: scaleX(-1);color:red;display:none"></p>
								</a>
							</li>
							<li>
								<a href="#step-4">
									<span class="step_no">4</span>
									<span class="step_descr">
										Departure from Site<br />
										<small>الانطلاق من الموقع</small><br />
										<small id="time-step4"></small><br />
									</span>
									<p id="amb-step4" class="fa fa-ambulance" style="font-size:48px;-webkit-transform: scaleX(-1);transform: scaleX(-1);color:red;display:none;"></p>
								</a>
							</li>
							<li>
								<a href="#step-5">
									<span class="step_no">5</span>
									<span class="step_descr">
										Arrival to Destination<br />
										<small>الوصول</small><br />
										<small id="time-step5"></small><br />
									</span>
									<p id="amb-step5" class="fa fa-ambulance" style="font-size:48px;-webkit-transform: scaleX(-1);transform: scaleX(-1);color:red;display:none;"></p>
								</a>
							</li>
							<li>
								<a href="#step-6">
									<span class="step_no">6</span>
									<span class="step_descr">
										Unit Ready<br />
										<small>الوحدة جاهزة</small><br />
										<small id="time-step6"></small><br />
									</span>
									<p id="amb-step6" class="fa fa-ambulance" style="font-size:48px;-webkit-transform: scaleX(-1);transform: scaleX(-1);color:red;display:none;"></p>
								</a>
							</li>
							<li>
								<a href="#step-7">
									<span class="step_no">7</span>
									<span class="step_descr">
										Return to Station<br />
										<small>العودة إلى المركز</small><br />
										<small id="time-step7"></small><br />
									</span>
									<p id="amb-step7" class="fa fa-ambulance" style="font-size:48px;-webkit-transform: scaleX(-1);transform: scaleX(-1);color:red;display:none;"></p>
								</a>
							</li>
						</ul>
						<div id="step-1" style="overflow:hidden;">
							<div class="form-group">
								<div class="row">
									<div class="col-md-2"></div>
									<div class="col-md-8">
										<div id="datetimepicker1"></div>
									</div>
									<div class="col-md-2"></div>
								</div>
							</div>
						</div>
						<div id="step-2">
							<div class="form-group">
								<div class="row">
									<div class="col-md-2"></div>
									<div class="col-md-8">
										<div id="datetimepicker2"></div>
									</div>
									<div class="col-md-2"></div>
								</div>
							</div>
						</div>
						<div id="step-3">
							<div class="form-group">
								<div class="row">
									<div class="col-md-2"></div>
									<div class="col-md-8">
										<div id="datetimepicker3"></div>
									</div>
									<div class="col-md-2"></div>
								</div>
							</div>
						</div>
						<div id="step-4">
							<div class="form-group">
								<div class="row">
									<div class="col-md-2"></div>
									<div class="col-md-8">
										<div id="datetimepicker4"></div>
									</div>
									<div class="col-md-2"></div>
								</div>
							</div>
						</div>
						<div id="step-5">
							<div class="form-group">
								<div class="row">
									<div class="col-md-2"></div>
									<div class="col-md-8">
										<div id="datetimepicker5"></div>
									</div>
									<div class="col-md-2"></div>
								</div>
							</div>
						</div>
						<div id="step-6">
							<div class="form-group">
								<div class="row">
									<div class="col-md-2"></div>
									<div class="col-md-8">
										<div id="datetimepicker6"></div>
									</div>
									<div class="col-md-2"></div>
								</div>
							</div>
						</div>
						<div id="step-7">
							<div class="form-group">
								<div class="row">
									<div class="col-md-2"></div>
									<div class="col-md-8">
										<div id="datetimepicker7"></div>
									</div>
									<div class="col-md-2"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<h2>Emergency Information</h2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<label>
							<input id="yamli_switch" type="checkbox" class="js-switch">&nbsp;&nbsp;<small>Enable/Disable Yamli</small>
						</label>
						<ul class="nav navbar-right panel_toolbox">
							<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
							</li>
						</ul>
						<div class="clearfix"></div>
					</div>
					<div class="x_content">
						<div class="" role="tabpanel" data-example-id="togglable-tabs">
							<ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
								<li role="presentation" class="active"><a href="#tab_content1" id="general-tab" role="tab" data-toggle="tab" aria-expanded="true">General</a>
								</li>
								<li role="presentation" class=""><a href="#tab_content8" id="transaport-tab" role="tab" data-toggle="tab" aria-expanded="false">Transport</a>
								</li>
								<li role="presentation" class=""><a href="#tab_content2" role="tab" id="location-tab" data-toggle="tab" aria-expanded="false">Location</a>
								</li>
								<li role="presentation" class=""><a href="#tab_content3" role="tab" id="cases-tab" data-toggle="tab" aria-expanded="false">Cases</a>
								</li>
								<li role="presentation" class=""><a href="#tab_content4" role="tab" id="team-tab" data-toggle="tab" aria-expanded="false">Team</a>
								</li>
								<li role="presentation" class=""><a href="#tab_content5" role="tab" id="support-tab" data-toggle="tab" aria-expanded="false">Support</a>
								</li>
								<li role="presentation" class=""><a href="#tab_content6" role="tab" id="annuler-tab" data-toggle="tab" aria-expanded="false">Annuler</a>
								</li>
								<li role="presentation" class=""><a href="#tab_content7" role="tab" id="dispacth-tab" data-toggle="tab" aria-expanded="false">Dispatch</a>
								</li>
							</ul>
							<div id="myTabContent" class="tab-content">
								<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="general-tab">
									<label>Emergency Type *:</label>
									<p>
										<input type="radio" class="flat" name="type" id="avp" value="avp" checked="" required />&nbsp;AVP&nbsp;
										<input type="radio" class="flat" name="type" id="urgence" value="urgence" />&nbsp;URGENCE&nbsp;
										<input type="radio" class="flat" name="type" id="transport" value="transport" />&nbsp;TRANSPORT&nbsp;
									</p>
									<hr>
									<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
										<label for="source">Source :&nbsp;</label>
										<select name="source" data-placeholder="Choose a Source..." class="form-control standardSelect" tabindex="-1" id="source" style="display:none;">
											<option value="-1" selected></option>
											@foreach($sources as $source)
											<option value="{{ $source->id }}">{{ $source->description }}</option>
											@endforeach
										</select>
										<!--<input type="text" maxlength="100" class="form-control has-feedback-left" id="source" placeholder="Source">
										<span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>-->
									</div>
									<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
										<input type="text" maxlength="20" class="form-control has-feedback-left" id="source_phone" placeholder="Phone" value="140">
										<span class="fa fa-phone form-control-feedback left" aria-hidden="true"></span>
									</div>
									<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
										<input type="text" maxlength="200" class="form-control has-feedback-left yamli-target" id="waiting" placeholder="Someone Waiting">
										<span class="fa fa-taxi form-control-feedback left" aria-hidden="true"></span>
									</div>
									<div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
										<input type="text" maxlength="200" class="form-control has-feedback-left yamli-target" id="cause_of_incident" placeholder="Cause of Incident">
										<span class="fa fa-arrow-circle-right form-control-feedback left" aria-hidden="true"></span>
									</div>
									<div class="col-md-12 col-sm-12 col-xs-12 form-group" id="all_cases">
										<div class="row">
											<div class="col-md-12">
												<a href='#all_cases' id="add_patient" class='btn btn-info btn-lg'><i class='fa fa-pencil'>Add Patient</i></a>
											</div>
										</div>
									</div>
									<table>
										<tr>
											<td style="width:200px;">
												<div class="form-group" >
													<label class="control-label col-md-3 col-sm-3 col-xs-3">From &nbsp;&nbsp;&nbsp;</label>
													<div class="col-md-9 col-sm-9 col-xs-9">
														<select data-placeholder="Choose where from..." id="select_from" class="form-control from standardSelect" name="from" onchange="change_from(this)" tabindex="-1" style="display:none;">
															<option value="-1"></option>
															<option value="0">Hospital</option>
															<option value="1">House</option>
															<option value="2">Road</option>
															<option value="3">Station</option>
															<option value="4">Others</option>
														</select>
													</div>
												</div>
											</td>
											<td style="width:300px;">
												<div class="form-group" >
													<span id="fs0" style="display:none;">
														<select dropup class="form-control from_specific_0 standardSelect" data-placeholder="Choose a hospital..." name="from_specific_0" tabindex="-1" style="width:240px;display:none">
															<option value="-1"></option>
															@foreach($hospitals as $hospital)
															<option value="{{ $hospital->id }}">{{ $hospital->code . " (" . $hospital->name . ")" }}</option>
															@endforeach
														</select>
													</span>
													<span id="fs1" style="display:none;">
														<select dropup class="form-control from_specific_1 standardSelect" name="from_specific_1" data-placeholder="Choose a location..." style="width:240px;display:none" tabindex="-1">
															<option value="-1"></option>
															@foreach($locations as $location)
															<option value = "{{ $location->id }}">{{ $location->village . " (" . $location->station . ")" }}</option>
															@endforeach
														</select>
													</span>
													<span id="fs3" style="display:none;">
														<select dropup class="form-control from_specific_3 standardSelect" name="from_specific_3" style="width:240px;display:none" tabindex="-1" data-placeholder="Choose a Station...">
															<option value="-1"></option>
															@foreach($stations as $station)
															<option value = "{{ $station->id }}">{{ $station->id . " (" . $station->name . ")"  }}</option>
															@endforeach
														</select>
													</span>
													<span id="fs2" style="display:none;">
														<input type="text" maxlength="100" class="form-control from_specific_2 yamli-target" name="from_specific_2" placeholder="From ... " style="width:240px;">
													</span>
												</div>
											</td>
										</tr>
										<tr>
											<td class="error_td">
												<span id="from_error" class="from_error error"></span>
											</td>
										</tr>
										<tr>
											<td style="width:200px;">
												<div class="form-group" >
													<label class="control-label col-md-3 col-sm-3 col-xs-3">To   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
													<div class="col-md-9 col-sm-9 col-xs-9">
														<select data-placeholder="Choose where to..." id="select_to" class="form-control to standardSelect" name="to" onchange="change_to(this)"  tabindex="-1" style="display:none;">
															<option value="-1"></option>
															<option value="0">Hospital</option>
															<option value="1">House</option>
															<option value="2">Road</option>
															<option value="3">Station</option>
															<option value="4">Others</option>
														</select>
													</div>
												</div>
											</td>
											<td>
												<div class="form-group" >
													<span id="ts0" style="display:none;">
														<select class="form-control to_specific_0 standardSelect" name="to_specific_0" data-placeholder="Choose a hospital..." style="width:240px;display:none" tabindex="-1">
															<option value="-1"></option>
															@foreach($hospitals as $hospital)
															<option value="{{ $hospital->id }}">{{ $hospital->code . " (" . $hospital->name . ")" }}</option>
															@endforeach
														</select>
													</span>
													<span id="ts1" style="display:none;">
														<select class="form-control to_specific_1 standardSelect" name="to_specific_1" data-placeholder="Choose a location..." style="width:240px;display:none" tabindex="-1">
															<option value="-1"></option>
															@foreach($locations as $location)
															<option value = "{{ $location->id }}">{{ $location->village . " (" . $location->station . ")" }}</option>
															@endforeach
														</select>
													</span>
													<span id="ts3" style="display:none;">
														<select class="form-control to_specific_3 standardSelect" name="to_specific_3" data-placeholder="Choose a Station..." style="width:240px;display:none" tabindex="-1">
															<option value="-1"></option>
															@foreach($stations as $station)
															<option value = "{{ $station->id }}">{{ $station->id . " (" . $station->name . ")"  }}</option>
															@endforeach
														</select>
													</span>
													<span id="ts2" style="display:none;">
														<input type="text" maxlength="100" class="form-control to_specific_2 yamli-target" name="to_specific_2" placeholder="To ... " style="width:240px;">
													</span>                                                                                
												</div>
											</td>
										</tr>
										<tr>
											<td class="error_td">
												<span class="to_error error" id="to_error"></span>
											</td>
										</tr>
									</table>
									<label for="location_message">More Information / معلومات إضافية</label>
									<textarea id="location_message" maxlength="200" class="form-control yamli-target" name="location_message"></textarea>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="tab_content8" aria-labelledby="transport-tab">
									<fieldset id="transport-fieldset" disabled="disabled">
										<div class="form-group">
											<div class="row">
												<div class="col-md-6 col-sm-12 col-xs-12 form-group has-feedback">
													<label for="transport_time">Transport Time</label>
													<input type="text" class="form-control has-feedback-left" id="transport_time" placeholder="Transport Time">
													<span class="fa fa-clock-o form-control-feedback left" aria-hidden="true"></span>
												</div>
												<div class="col-md-4 col-sm-12 col-xs-12 form-group has-feedback">
													<label>Transport Type *:</label>
													<p>
														H-H : <input type="radio" class="flat" name="ttype" id="hh" value="hh" checked="" required />&nbsp;&nbsp;&nbsp;
														H-&#916; : <input type="radio" class="flat" name="ttype" id="hd" value="hd" />&nbsp;&nbsp;&nbsp;
														&#916-H : <input type="radio" class="flat" name="ttype" id="dh" value="dh" />&nbsp;&nbsp;&nbsp;
													</p>
												</div>
												<div class="col-md-2 col-sm-12 col-xs-12 form-group has-feedback">
													<label>
														<input id="gamma_switch" type="checkbox" class="js-switch">Reported to Gamma
													</label>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6 col-sm-12 col-xs-12 form-group has-feedback">
													<label for="admission">Admission</label>
													<input type="text" maxlength="100" class="form-control has-feedback-left" id="admission" placeholder="Admission">
													<span class="fa fa-user-md form-control-feedback left" aria-hidden="true"></span>
												</div>
												<div class="col-md-6 col-sm-12 col-xs-12 form-group has-feedback">
													<label for="doctor">Doctor</label>
													<input type="text" maxlength="100" class="form-control has-feedback-left" id="doctor" placeholder="Doctor">
													<span class="fa fa-stethoscope form-control-feedback left" aria-hidden="true"></span>
												</div>
											</div>
											<div class="row">
												<div class="col-md-6 col-sm-12 col-xs-12 form-group has-feedback">
													<label for="reason_for_ambulance">Reason for Ambulance</label>
													<input type="text" maxlength="200" class="form-control has-feedback-left" id="reason_for_ambulance" placeholder="Reason For Ambulance">
													<span class="fa fa-space-shuttle form-control-feedback left" aria-hidden="true"></span>
												</div>
												<div class="col-md-6 col-sm-12 col-xs-12 form-group has-feedback">
													<label for="cleared_ready">Cleared Balance / Patient Ready</label>
													<input type="text" maxlength="100" class="form-control has-feedback-left" id="cleared_ready" placeholder="Cleared Balance / Patient Ready">
													<span class="fa fa-flag-checkered form-control-feedback left" aria-hidden="true"></span>
												</div>
											</div>
											<div class="row">
												<div class="col-md-3 col-sm-12 col-xs-12 form-group has-feedback">
													<!--<label for="o2">O<sub>2</sub> (L)</label>
													<input type="number" class="form-control has-feedback-left" id="o2" placeholder="O2 (L)">
													<span class="fa fa-tint form-control-feedback left" aria-hidden="true"></span>-->
													<label>
														<input id="o2_switch" type="checkbox" class="js-switch">O2
													</label>
												</div>
												<div class="col-md-3 col-sm-12 col-xs-12 form-group has-feedback">
													<!--<label for="matelas">Matelas</label>
													<input type="text" maxlength="200" class="form-control has-feedback-left" id="matelas" placeholder="Matelas">
													<span class="fa fa-bed form-control-feedback left" aria-hidden="true"></span>-->
													<label>
														<input id="matelas_switch" type="checkbox" class="js-switch">Matelas
													</label>
												</div>
												<div class="col-md-3 col-sm-12 col-xs-12 form-group has-feedback">
													<!--<label for="couveuse">Couveuse</label>
													<input type="text" maxlength="200" class="form-control has-feedback-left" id="couveuse" placeholder="Couveuse">
													<span class="fa fa-child form-control-feedback left" aria-hidden="true"></span>-->
													<label>
														<input id="couveuse_switch" type="checkbox" class="js-switch">Couveuse
													</label>
												</div>
												<div class="col-md-3 col-sm-12 col-xs-12 form-group has-feedback">
													<!--<label for="respirateur">Respirateur</label>
													<input type="text" maxlength="200" class="form-control has-feedback-left" id="respirateur" placeholder="Respirateur">
													<span class="fa fa-heartbeat form-control-feedback left" aria-hidden="true"></span>-->
													<label>
														<input id="respirateur_switch" type="checkbox" class="js-switch">Respirateur
													</label>
												</div>
											</div>
										</div>
									</fieldset>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="cases-tab">
									Moved to General Tab
								</div>
								<div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="cases-tab">
									<!--<label for="cases_message">Cases Information (Name / Age / Case)</label>
									<textarea id="cases_message" class="form-control yamli-target" name="cases_message"></textarea>-->
									Moved to Genral Tab
								</div>
								<div role="tabpanel" class="tab-pane fade" id="tab_content4" aria-labelledby="team-tab">
									<div class="form-group">
										<div class="row">
											<div class="col-md-6">
												<label for="car_number">Ambulance :&nbsp;</label>
												<select name="car_number" data-placeholder="Choose an Ambulance..." class="form-control standardSelect" tabindex="-1" id="car_number" style="display:none;">
                                                    <option value="-1" selected></option>
                                                    @foreach($ambulances as $ambulance)
                                                    <option value="{{ $ambulance->id }}">{{ $ambulance->number }}</option>
                                                    @endforeach
                                                </select>
											</div>
											<div class="col-md-6">
												<label for="driver">Driver :&nbsp;</label>
												<select class="form-control standardSelect" data-placeholder="Choose the Driver..."  id="driver" name="driver" tabindex="-1" style="display:none;">
                                                    <option value="-1"></option>
                                                    @foreach($drivers as $driver)
                                                    <option value="{{ $driver->id }}">{{ $driver->nickname }}</option>
                                                    @endforeach
                                                </select>
											</div>
										</div>
										<div class="clearfix"></div>
										<br />
										<div class="row">
											<div class="col-md-6">
												<label for="m_leader">Mission Leader :&nbsp;</label>
												<select class="form-control standardSelect" id="m_leader" data-placeholder="Choose the Mission Leader..." name="m_leader" tabindex="-1" style="display:none;">
                                                    <option value="-1"></option>
                                                    @foreach($emts as $emt)
                                                    <option value="{{ $emt->id }}">{{ $emt->nickname }}</option>
                                                    @endforeach
                                                </select>
											</div>
											<div class="col-md-6">
												<label for="emts">Emts :&nbsp;</label>
												<select multiple id="emts" name="emt" data-placeholder="Choose all EMTs..." class="standardSelect" tabindex="-1" style="display: none;">                                                    <option value="-1"></option>
                                                    @foreach($emts as $emt)
                                                    <option value="{{ $emt->id }}">{{ $emt->nickname }}</option>
                                                    @endforeach
                                                </select>
											</div>
										</div>
										<div class="clearfix"></div>
										<br />
										<div class="row">
											<div class="col-md-12">
												<label for="other_emts">Other Emts</label>
												<textarea id="other_emts" maxlength="100" class="form-control" name="other_emts"></textarea>
											</div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="tab_content5" aria-labelledby="support-tab">
									<div class="form-group">
										<div class="row">
											<div class="col-md-2">
												<label>
													<input id="anyway_switch" type="checkbox" class="js-switch">Anyway
												</label>
											</div>
											<div class="col-md-5">
												<input type="text" class="form-control has-feedback-left" id="anyway_time" placeholder="Time">
												<span class="fa fa-clock-o form-control-feedback left" aria-hidden="true"></span>
											</div>
											<div class="col-md-5">
												<input type="text" maxlength="100" class="form-control has-feedback-left" id="anyway_resp" placeholder="Notes">
												<span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
											</div>
										</div>
										<br />
										<div class="clearfix"></div>
										<div class="row">
											<div class="col-md-2">
												<label>
													<input id="dc_switch" type="checkbox" class="js-switch">DC
												</label>
											</div>
											<div class="col-md-5">
												<input type="text" class="form-control has-feedback-left" id="dc_time" placeholder="Time">
												<span class="fa fa-clock-o form-control-feedback left" aria-hidden="true"></span>
											</div>
											<div class="col-md-5">
												<input type="text" maxlength="100" class="form-control has-feedback-left" id="dc_resp" placeholder="Notes">
												<span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
											</div>
										</div>
										<br />
										<div class="clearfix"></div>
										<div class="row">
											<div class="col-md-2">
												<label>
													<input id="ambulance_switch" type="checkbox" class="js-switch">Ambulance
												</label>
											</div>
											<div class="col-md-5">
												<input type="text" class="form-control has-feedback-left" id="ambulance_time" placeholder="Time" >
												<span class="fa fa-clock-o form-control-feedback left" aria-hidden="true"></span>
											</div>
											<div class="col-md-5">
												<input type="text" maxlength="100" class="form-control has-feedback-left" id="ambulance_resp" placeholder="Notes">
												<span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
											</div>
										</div>
										<br />
										<div class="clearfix"></div>
										<div class="row">
											<div class="col-md-2">
												<label>
													<input id="sar_switch" type="checkbox" class="js-switch">SAR
												</label>
											</div>
											<div class="col-md-5">
												<input type="text" class="form-control has-feedback-left" id="sar_time" placeholder="Time">
												<span class="fa fa-clock-o form-control-feedback left" aria-hidden="true"></span>
											</div>
											<div class="col-md-5">
												<input type="text" maxlength="100" class="form-control has-feedback-left" id="sar_resp" placeholder="Notes">
												<span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
											</div>
										</div>
										<br />
										<div class="clearfix"></div>
										<div class="row">
											<div class="col-md-12">
												<label for="support_info">More Information</label>
												<textarea id="support_info" maxlength="200" class="form-control yamli-target" name="support_info"></textarea>
											</div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="tab_content6" aria-labelledby="annuler-tab">
									<div class="form-group">
										<div class="row">
											<div class="col-md-2">
												<label>
													<input id="annuler_switch" type="checkbox" class="js-switch">Annuler
												</label>
											</div>
											<div class="col-md-4">
												<input type="text" maxlength="100" class="form-control has-feedback-left yamli-target" id="annuler_reason" placeholder="Reason">
												<span class="fa fa-space-shuttle form-control-feedback left" aria-hidden="true"></span>
											</div>
											<div class="col-md-3">
												<input type="text" maxlength="100" class="form-control has-feedback-left" id="annuler_source" placeholder="Source">
												<span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
											</div>
											<div class="col-md-3">
												<input type="text" class="form-control has-feedback-left" id="annuler_time" placeholder="Time">
												<span class="fa fa-clock-o form-control-feedback left" aria-hidden="true"></span>
											</div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="tab_content7" aria-labelledby="dispatch-tab">
									<div class="form-group">
										<div class="row">
											<div class="col-md-6">
												<label for="dispatch_1">Dispatch 1 :&nbsp;</label>
												<select class="form-control standardSelect" id="dispatch_1" data-placeholder="Dispatch 1..." name="dispatch_1" tabindex="-1" style="display:none;">
                                                    <option value="-1"></option>
                                                    @foreach($emts as $emt)
                                                    <option value="{{ $emt->id }}">{{ $emt->nickname }}</option>
                                                    @endforeach
                                                </select>
											</div>
											<div class="col-md-6">
												<label for="dispatch_2">Dispatch 2 :&nbsp;</label>
												<select class="form-control standardSelect" id="dispatch_2" data-placeholder="Dispatch 2..." name="dispatch_2" tabindex="-1" style="display:none;">
                                                    <option value="-1"></option>
                                                    @foreach($emts as $emt)
                                                    <option value="{{ $emt->id }}">{{ $emt->nickname }}</option>
                                                    @endforeach
                                                </select>
											</div>
										</div>
										<br/>
										<div class="clearfix"></div>
										<div class="row">
											<div class="col-md-6">
												<input type="text" maxlength="100" class="form-control has-feedback-left" id="or_1" placeholder="OR 1">
												<span class="fa fa-phone form-control-feedback left" aria-hidden="true"></span>
											</div>
											<div class="col-md-6">
												<input type="text" maxlength="100" class="form-control has-feedback-left" id="or_2" placeholder="OR 2">
												<span class="fa fa-phone form-control-feedback left" aria-hidden="true"></span>
											</div>
										</div>
										<br/>
										<div class="clearfix"></div>
										<div class="row">
											<div class="col-md-6">
												<label for="dispatch_leader">Dispatch Leader :&nbsp;</label>
												<select class="form-control standardSelect" id="dispatch_leader" data-placeholder="Dispatch Leader..." name="dispatch_leader" tabindex="-1" style="display:none;">
                                                    <option value="-1"></option>
                                                    @foreach($emts as $emt)
                                                    <option value="{{ $emt->id }}">{{ $emt->nickname }}</option>
                                                    @endforeach
                                                </select>
											</div>
											<div class="col-md-6">
												<label for="dispatch_comment">Comment :&nbsp;</label>
												<textarea class="form-control" maxlength="200" id="dispatch_comment yamli-target" placeholder="Comment"></textarea>
											</div>
										</div>
										<br/>
										<div class="clearfix"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<h2>Actions</h2>
						<div class="clearfix"></div>
					</div>
					<div class="x_content">
						<div class="form-group">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<button type=button" class="btn btn-primary btn-lg" onclick="location.reload(false)">Reset</button>
								<button type=button" class="btn btn-success btn-lg" onclick="saveForm()">Submit</button>
								<input type="text" style="display:none" id="form_id" value="0"></input>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>			
	</div>
</div>

<div id="save_modal" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span>
				</button>
				<h4 class="modal-title" id="myModalLabel2">Save Emergency Call</h4>
			</div>
			<div class="modal-body">
				<p id="save_modal_text"></p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default btn_save_modal" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<!-- YAMLI CODE START -->
<!--<script type="text/javascript" src="https://api.yamli.com/js/yamli_api.js"></script>
<script type="text/javascript">
  if (typeof(Yamli) == "object" && Yamli.init( { uiLanguage: "en" , startMode: "onOrUserDefault" } ))
  {
    Yamli.yamlify( "chiefComplaint", { settingsPlacement: "inside" } );
    Yamli.yamlify( "comment", { settingsPlacement: "inside" } );
  }
</script>-->
<!-- YAMLI CODE END -->
<script src="../vendors/jQuery-Smart-Wizard/js/jqeury-smartWizard.js" type="text/javascript"></script>
<script src="../vendors/datetimepicker/datetimepicker.min.js" type="text/javascript"></script>
<script src="../vendors/iCheck/icheck.min.js" type="text/javascript"></script>
<script src="../vendors/switchery/dist/switchery.min.js" type="text/javascript"></script>

<script type="text/javascript" src="https://api.yamli.com/js/yamli_api.js"></script>
<script type="text/javascript">
	if (typeof(Yamli) == "object" && Yamli.init( { uiLanguage: "en" , startMode: "onOrUserDefault" } ) ) {
		//Yamli.yamlifyClass( "yamli-target", { settingsPlacement: "bottom" , uiFontFamily : "monospace"} );
		//Yamli.deyamlifyClass("yamli-target");
	}
</script>
<!-- YAMLI CODE END -->
<script>

$(document).ready(function() {
	
	new Switchery($('#o2_switch'),{color:'#FF0000'});
	new Switchery($('#matelas_switch'),{color:'#FF0000'});
	new Switchery($('#couveuse_switch'),{color:'#FF0000'});
	new Switchery($('#respirateur_switch'),{color:'#FF0000'});
	
	new Switchery($('#anyway_switch'),{color:'#FF0000'});
	new Switchery($('#dc_switch'),{color:'#FF0000'});
	new Switchery($('#ambulance_switch'),{color:'#FF0000'});
	new Switchery($('#sar_switch'),{color:'#FF0000'});
	new Switchery($('#annuler_switch'),{color:'#FF0000'});
	new Switchery($('#yamli_switch'),{color:'#FF0000'});
	new Switchery($('#gamma_switch'),{color:'#FF0000'});
	
	
	
	$('#tab_content5').find("input[type=text]").each(function(){ 
		$(this).prop("disabled" , true);
	});
	$('#tab_content6').find("input[type=text]").each(function(){ 
		$(this).prop("disabled" , true);
	});
	
	$('.js-switch').on("change", function(event) {
		
		var elem = $(event.target).parent().parent().parent();
		if( $(event.target).attr('id')=='yamli_switch' ){
			if ( event.target.checked==true ){
				if (typeof(Yamli) == "object" && Yamli.init( { uiLanguage: "en" , startMode: "onOrUserDefault" } ) ) {
					Yamli.yamlifyClass( "yamli-target", { settingsPlacement: "inside" , uiFontFamily : "monospace"} );
				}
			} else {
				if (typeof(Yamli) == "object" && Yamli.init( { uiLanguage: "en" , startMode: "onOrUserDefault" } ) ) {
					Yamli.deyamlifyClass("yamli-target");
				}
			}
				
		} else if( $(event.target).attr('id')=='gamma_switch' ){ 
			//Do nothing
		} else if( $(event.target).attr('id')=='anyway_switch' ){ 
			if ( event.target.checked==true ){
				elem.find("input[type=text]").each(function(){ 
					$(this).prop("disabled" , false);
				});
				if( $('#anyway_time').data("DateTimePicker").date() == null ) {
					$('#anyway_time').data("DateTimePicker").date(new Date());
				}
			} else {
				elem.find("input[type=text]").each(function(){
					$(this).prop("disabled" , true);
				});
			}
		} else if( $(event.target).attr('id')=='dc_switch' ){ 
			if ( event.target.checked==true ){
				elem.find("input[type=text]").each(function(){ 
					$(this).prop("disabled" , false);
				});
				if( $('#dc_time').data("DateTimePicker").date() == null ) {
					$('#dc_time').data("DateTimePicker").date(new Date());
				}
			} else {
				elem.find("input[type=text]").each(function(){
					$(this).prop("disabled" , true);
				});
			}
		} else if( $(event.target).attr('id')=='ambulance_switch' ){ 
			if ( event.target.checked==true ){
				elem.find("input[type=text]").each(function(){ 
					$(this).prop("disabled" , false);
				});
				if( $('#ambulance_time').data("DateTimePicker").date() == null ) {
					$('#ambulance_time').data("DateTimePicker").date(new Date());
				}
			} else {
				elem.find("input[type=text]").each(function(){
					$(this).prop("disabled" , true);
				});
			}
		} else if( $(event.target).attr('id')=='sar_switch' ){ 
			if ( event.target.checked==true ){
				elem.find("input[type=text]").each(function(){ 
					$(this).prop("disabled" , false);
				});
				if( $('#sar_time').data("DateTimePicker").date() == null ) {
					$('#sar_time').data("DateTimePicker").date(new Date());
				}
			} else {
				elem.find("input[type=text]").each(function(){
					$(this).prop("disabled" , true);
				});
			}
		} else {
			if ( event.target.checked==true ){
				elem.find("input[type=text]").each(function(){ 
					$(this).prop("disabled" , false);
				});
			} else {
				elem.find("input[type=text]").each(function(){
					$(this).prop("disabled" , true);
				});
			}
		}
		
		
	});
	
	$('#datetimepicker1').datetimepicker({
		inline: true,
		sideBySide: true,
		format : 'YYYY-MM-DD HH:mm'
	});
	$('#datetimepicker2').datetimepicker({
		inline: true,
		sideBySide: true,
		useCurrent: false,
		format : 'YYYY-MM-DD HH:mm'
	});
	$('#datetimepicker3').datetimepicker({
		inline: true,
		sideBySide: true,
		useCurrent: false,
		format : 'YYYY-MM-DD HH:mm'
	});
	$('#datetimepicker4').datetimepicker({
		inline: true,
		sideBySide: true,
		useCurrent: false,
		format : 'YYYY-MM-DD HH:mm'
	});
	$('#datetimepicker5').datetimepicker({
		inline: true,
		sideBySide: true,
		useCurrent: false,
		format : 'YYYY-MM-DD HH:mm'
	});
	$('#datetimepicker6').datetimepicker({
		inline: true,
		sideBySide: true,
		useCurrent: false,
		format : 'YYYY-MM-DD HH:mm'
	});
	$('#datetimepicker7').datetimepicker({
		inline: true,
		sideBySide: true,
		useCurrent: false,
		format : 'YYYY-MM-DD HH:mm'
	});
	$('#annuler_time').datetimepicker({format : 'YYYY-MM-DD HH:mm'});
	$('#anyway_time').datetimepicker({format : 'YYYY-MM-DD HH:mm'});
	$('#dc_time').datetimepicker({format : 'YYYY-MM-DD HH:mm'});
	$('#ambulance_time').datetimepicker({format : 'YYYY-MM-DD HH:mm'});
	$('#sar_time').datetimepicker({format : 'YYYY-MM-DD HH:mm'});
	$('#transport_time').datetimepicker({format : 'YYYY-MM-DD HH:mm'});
	
	
	
	$("#datetimepicker1").on("dp.change", function (e) {
		$('#time-step1').text( moment($('#datetimepicker1').data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm') );
		if( $('#datetimepicker2').data("DateTimePicker").date() != null && $('#datetimepicker2').data("DateTimePicker").date().isBefore(e.date) ){
			$('#datetimepicker2').data("DateTimePicker").date(e.date);
		}
		//$('#datetimepicker2').data("DateTimePicker").minDate(e.date);
	});
	$("#datetimepicker2").on("dp.change", function (e) {
		$('#time-step2').text( moment($('#datetimepicker2').data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm') );
		if( $('#datetimepicker1').data("DateTimePicker").date() != null && $('#datetimepicker1').data("DateTimePicker").date().isAfter(e.date) ){
			$('#datetimepicker1').data("DateTimePicker").date(e.date);
		}
		if( $('#datetimepicker3').data("DateTimePicker").date() != null && $('#datetimepicker3').data("DateTimePicker").date().isBefore(e.date) ){
			$('#datetimepicker3').data("DateTimePicker").date(e.date);
		}
		//$('#datetimepicker1').data("DateTimePicker").maxDate(e.date);
		//$('#datetimepicker3').data("DateTimePicker").minDate(e.date);
	});
	$("#datetimepicker3").on("dp.change", function (e) {
		$('#time-step3').text( moment($('#datetimepicker3').data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm') );
		if( $('#datetimepicker2').data("DateTimePicker").date() != null && $('#datetimepicker2').data("DateTimePicker").date().isAfter(e.date) ){
			$('#datetimepicker2').data("DateTimePicker").date(e.date);
		}
		if( $('#datetimepicker4').data("DateTimePicker").date() != null && $('#datetimepicker4').data("DateTimePicker").date().isBefore(e.date) ){
			$('#datetimepicker4').data("DateTimePicker").date(e.date);
		}
		//$('#datetimepicker2').data("DateTimePicker").maxDate(e.date);
		//$('#datetimepicker4').data("DateTimePicker").minDate(e.date);
	});
	$("#datetimepicker4").on("dp.change", function (e) {
		$('#time-step4').text( moment($('#datetimepicker4').data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm') );
		if( $('#datetimepicker3').data("DateTimePicker").date() != null && $('#datetimepicker3').data("DateTimePicker").date().isAfter(e.date) ){
			$('#datetimepicker3').data("DateTimePicker").date(e.date);
		}
		if( $('#datetimepicker5').data("DateTimePicker").date() != null && $('#datetimepicker5').data("DateTimePicker").date().isBefore(e.date) ){
			$('#datetimepicker5').data("DateTimePicker").date(e.date);
		}
		//$('#datetimepicker3').data("DateTimePicker").maxDate(e.date);
		//$('#datetimepicker5').data("DateTimePicker").minDate(e.date);
	});
	$("#datetimepicker5").on("dp.change", function (e) {
		$('#time-step5').text( moment($('#datetimepicker5').data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm') );
		if( $('#datetimepicker4').data("DateTimePicker").date() != null && $('#datetimepicker4').data("DateTimePicker").date().isAfter(e.date) ){
			$('#datetimepicker4').data("DateTimePicker").date(e.date);
		}
		if( $('#datetimepicker6').data("DateTimePicker").date() != null && $('#datetimepicker6').data("DateTimePicker").date().isBefore(e.date) ){
			$('#datetimepicker6').data("DateTimePicker").date(e.date);
		}
		//$('#datetimepicker4').data("DateTimePicker").maxDate(e.date);
		//$('#datetimepicker6').data("DateTimePicker").minDate(e.date);
	});
	$("#datetimepicker6").on("dp.change", function (e) {
		$('#time-step6').text( moment($('#datetimepicker6').data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm') );
		if( $('#datetimepicker5').data("DateTimePicker").date() != null && $('#datetimepicker5').data("DateTimePicker").date().isAfter(e.date) ){
			$('#datetimepicker5').data("DateTimePicker").date(e.date);
		}
		if( $('#datetimepicker7').data("DateTimePicker").date() != null && $('#datetimepicker7').data("DateTimePicker").date().isBefore(e.date) ){
			$('#datetimepicker7').data("DateTimePicker").date(e.date);
		}
		//$('#datetimepicker5').data("DateTimePicker").maxDate(e.date);
		//$('#datetimepicker7').data("DateTimePicker").minDate(e.date);
	});
	$("#datetimepicker7").on("dp.change", function (e) {
		$('#time-step7').text( moment($('#datetimepicker7').data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm') );
		if( $('#datetimepicker6').data("DateTimePicker").date() != null && $('#datetimepicker6').data("DateTimePicker").date().isAfter(e.date) ){
			$('#datetimepicker6').data("DateTimePicker").date(e.date);
		}
		//$('#datetimepicker6').data("DateTimePicker").maxDate(e.date);
	});
	$('#datetimepicker1').data("DateTimePicker").date(new Date());
	
	$('#wizard').smartWizard({
		transitionEffect: 'slide',
		transitionSpeed : 50
	});
	
	$(".buttonNext").addClass("btn btn-success");
	$(".buttonPrevious").addClass("btn btn-primary");
	
	$(".standardSelect").select2({ width: "100%" });
		
	$("input.flat").iCheck({
		checkboxClass: 'icheckbox_flat-green',
		radioClass: 'iradio_flat-green'
	});
	
	$("#tab_content8 input.flat").iCheck('disable');
	
	var record = {!! $record !!};  
	
	if( record != null ){
		
		loadToPage(record);
	}
	
});

$("input.flat").on('ifChanged' , function(event) {
	
	var elem = $(event.target);
	if( elem[0].value=="transport" ){
		if ( elem[0].checked ) {
			$('#transport-fieldset').prop('disabled',false);
			$("#tab_content8 input.flat").iCheck('enable');
		} else {
			$('#transport-fieldset').prop('disabled',true);
			$("#tab_content8 input.flat").iCheck('disable');
		}
	}
});

var patientCount = 0;

$("#add_patient").on("click" , function(){
	appendPatient("","","");
});

function appendPatient(name , age , cas){
	var str =  "<div class='row patient_row'>																												" +
			   "	<div class='clearfix'></div>																											" +
			   "	<br />																																	" +
			   "	<div class='col-md-4'>																													" +
			   "		<input type='text' maxlength='100' class='form-control has-feedback-left case_name' value='"+name+"' placeholder='Name'>			" +
			   "		<span class='fa fa-user form-control-feedback left' aria-hidden='true'></span>														" +
			   "	</div>                                                                                                                      			" +
			   "	<div class='col-md-2'>                                                                                                      			" +
			   "		<input type='number' class='form-control has-feedback-left case_age' value='"+age+"' placeholder='Age'>                          	" +
			   "		<span class='fa fa-birthday-cake form-control-feedback left' aria-hidden='true'></span>                                 			" +
			   "	</div>                                                                                                                      			" +
			   "	<div class='col-md-5'>                                                                                                      			" +
			   "		<input type='text' maxlength='100' class='form-control has-feedback-left yamli-target case_case' value='"+cas+"' placeholder='Case'>" +
			   "		<span class='fa fa-folder form-control-feedback left' aria-hidden='true'></span>                                            		" +
			   "	</div>                                                                                                                          		" +
			   "	<div class='col-md-1'>                                                                                                          		" +
			   "		<a href='#all_cases' class='btn btn-danger btn-xs remove-row'><i class='fa fa-eraser'></i></a>                              		" +
			   "	</div>                                                                                                                          		" +
			   "</div>	                                                                                                                            		" ;
			  
	var elem = $.parseHTML(str);
	$("#all_cases").append(elem);
}

$("#all_cases").on("click",".remove-row",function(event){
	var elem = $(event.target);
	var counter=10;
	while ( counter >= 0 && !elem.hasClass("row") ){
		counter--;
		elem = elem.parent();
	}
	elem.remove();
});

$("#car_number").on("change",function(a,b,c){
	$(document).prop('title' , 'Emergency Call ' + $("#car_number").select2('data')[0].text );
	return true;
});

function loadTimes(record){
	var interval = 450;
	
	if(record["call_time"] != null){
		$('#datetimepicker1').data("DateTimePicker").date(new Date(record["call_time"]));
	}
	setTimeout(function(){
		if(record["dep_to_site"] != null){
			$('.buttonNext')[0].click();
			$('#datetimepicker2').data("DateTimePicker").date(new Date(record["dep_to_site"]));
		}
	}, 1*interval);
	setTimeout(function(){
		if(record["arrival_on_site"] != null){
			$('.buttonNext')[0].click();
			$('#datetimepicker3').data("DateTimePicker").date(new Date(record["arrival_on_site"]));
		}
	},2*interval);
	setTimeout(function(){
		if(record["dep_to_dest"] != null){
			$('.buttonNext')[0].click();
			$('#datetimepicker4').data("DateTimePicker").date(new Date(record["dep_to_dest"]));
		}
	},3*interval);
	setTimeout(function(){
		if(record["arrival_to_dest"] != null){
			$('.buttonNext')[0].click();
			$('#datetimepicker5').data("DateTimePicker").date(new Date(record["arrival_to_dest"]));
		}
	},4*interval);
	setTimeout(function(){
		if(record["unit_ready"] != null){
			$('.buttonNext')[0].click();
			$('#datetimepicker6').data("DateTimePicker").date(new Date(record["unit_ready"]));
		}
	},5*interval);
	setTimeout(function(){
		if(record["return_to_station"] != null){
			$('.buttonNext')[0].click();
			$('#datetimepicker7').data("DateTimePicker").date(new Date(record["return_to_station"]));
		}
	},6*interval);
}

function loadToPage(record){
	
	$('#form_id').val(record["id"]);
	
	loadTimes(record);
	loadGeneral(record);
	loadLocation(record);
	loadCases(record);
	loadTeam(record);
	loadSupport(record);
	loadAnnuler(record);
	loadDispatch(record);
	loadTransport(record);
}

function loadTransport(record){
	
	if( record["emergency_type"]=="TRANSPORT" ){
		if( record["transport_time"]!=null ){
			$("#transport_time").data("DateTimePicker").date(new Date(record["transport_time"]));
		}
		if( record["transport_type"]=="hh" ){
			$("#hh").iCheck('check');
		} else if( record["transport_type"]=="hd" ){
			$("#hd").iCheck('check');
		} else if( record["transport_type"]=="dh" ){
			$("#dh").iCheck('check');
		}
		if( record["gamma"]==1 ){
			$("#gamma_switch").click();
		}
		$('#admission').val(record['admission']);
		$('#doctor').val(record['doctor']);
		$('#reason_for_ambulance').val(record['reason_for_ambulance']);
		$('#cleared_ready').val(record['cleared_ready']);
		if(record["o2"]!=0) $("#o2_switch").click();
		if(record["matelas"]!=0) $("#matelas_switch").click();
		if(record["couveuse"]!=0) $("#couveuse_switch").click();
		if(record["respirateur"]!=0) $("#respirateur_switch").click();
		/*$('#o2').val(record['o2']);
		$('#matelas').val(record['matelas']);
		$('#couveuse').val(record['couveuse']);
		$('#respirateur').val(record['respirateur']);*/
	}
}

function loadDispatch(record){
	$("#dispatch_1").val(record["dispatch_1"]).trigger("change");
	$("#dispatch_2").val(record["dispatch_2"]).trigger("change");
	$("#or_1").val(record["or_1"]);
	$("#or_2").val(record["or_2"]);
	$("#dispatch_leader").val(record["leader"]).trigger("change");
	$("#dispatch_comment").val(record["comment"]);
}

function loadAnnuler(record){
	if(record["annuler"]!=0){
		$("#annuler_switch").click();
		$("#annuler_reason").val(record["annuler_reason"]);
		$("#annuler_source").val(record["annuler_source"]);
		if( record["annuler_time"]!=null ){
			$("#annuler_time").data("DateTimePicker").date(new Date(record["annuler_time"]));
		}
	}
}

function loadSupport(record){
	loadSupportPartial(record,"anyway");
	loadSupportPartial(record,"dc");
	loadSupportPartial(record,"ambulance");
	loadSupportPartial(record,"sar");
	$("#support_info").val(record["support_info"]);
}

function loadSupportPartial(record,name){
	if( record[name + "_time"]!= null || record[name + "_resp"]!=null ){
		if( record[name + "_time"]!= null ) {
			$("#" + name + "_time").data("DateTimePicker").date(new Date(record[name + "_time"]));
		}
		if( record[name + "_time"]!= null ) {
			$("#" + name + "_resp").val(record[name + "_resp"]);
		}
		$("#" + name + "_switch").click();
	}
}

function loadTeam(record){
		$("#car_number").val(record["car_number"]).trigger("change");
		$("#driver").val(record["driver"]).trigger("change");
		$("#m_leader").val(record["team_leader"]).trigger("change");
		$("#emts").val(record["urg_emts"]).trigger("change");
		$("#other_emts").val(record["other_emts"]);
}

function loadCases(record){
	if(record["patients"]!=null && record["patients"].length > 0){
		for( var counter = 0 ; counter < record["patients"].length; counter++ ){
			var name = record["patients"][counter]["name"];
			var age = record["patients"][counter]["age"];
			var cas = record["patients"][counter]["cas"];
			appendPatient(name==null?"":name , age==null?"":age  , cas==null?"":cas );
		}
	}
}


function loadLocation(record){
	
	$("#select_from").val(record["from_type"]).trigger('change');
	$("#select_to").val(record["to_type"]).trigger('change');
	$("#location_message").val(record["location_info"]);
	
	var from_val = record["from_value"];
	var to_val = record["to_value"];
	
	switch(record["from_type"]) {
		case 0:
			$(".from_specific_0").val(from_val).trigger('change');
			break;
		case 1:
		case 2:
			$(".from_specific_1").val(from_val).trigger('change');
			break;
		case 3:
			$(".from_specific_3").val(from_val).trigger('change');
			break;
		case 4:
			$(".from_specific_2").val(from_val).trigger('change');
			break;
	}
	
	switch(record["to_type"]) {
		case 0:
			$(".to_specific_0").val(to_val).trigger('change');
			break;
		case 1:
		case 2:
			$(".to_specific_1").val(to_val).trigger('change');
			break;
		case 3:
			$(".to_specific_3").val(to_val).trigger('change');
			break;
		case 4:
			$(".to_specific_2").val(to_val).trigger('change');
			break;
	}	
}

function loadGeneral(record){
	$("#source").val(record["emergency_source"]).trigger("change");
	$("#source_phone").val(record["emergency_source_phone"]);
	$("#waiting").val(record["someone_waiting"]);
	$("#cause_of_incident").val(record["cause_of_incident"]);
	if( record["emergency_type"]=="URGENCE" ){
		$("#urgence").iCheck('check');
	} else if( record["emergency_type"]=="TRANSPORT" ){
		$("#transport").iCheck('check');
	}
}

function change_from(e){
        $par = $(e).parent().parent().parent().parent();
        $choice = $(e).val();
        switch ($choice) {
            case "-1":
                $par.find('#fs0').hide();
                $par.find('#fs1').hide();
                $par.find('#fs2').hide();
                $par.find('#fs3').hide();
                break;
            case "0":
                $par.find('#fs0').show();
                $par.find('#fs1').hide();
                $par.find('#fs2').hide();
                $par.find('#fs3').hide();
                break;
            case "1":
            case "2":
                $par.find('#fs0').hide();
                $par.find('#fs1').show();
                $par.find('#fs2').hide();
                $par.find('#fs3').hide();
                break;
            case "3":
                $par.find('#fs0').hide();
                $par.find('#fs1').hide();
                $par.find('#fs2').hide();
                $par.find('#fs3').show();
                break;
            case "4":
                $par.find('#fs0').hide();
                $par.find('#fs1').hide();
                $par.find('#fs2').show();
                $par.find('#fs3').hide();
                break;
            default:
                break;
        }
    }

    function change_to(e){
        $par = $(e).parent().parent().parent().parent();
        $choice = $(e).val();
        switch ($choice) {
            case "-1":
                $par.find('#ts0').hide();
                $par.find('#ts1').hide();
                $par.find('#ts2').hide();
                $par.find('#ts3').hide();
                break;
            case "0":
                $par.find('#ts0').show();
                $par.find('#ts1').hide();
                $par.find('#ts3').hide();
                $par.find('#ts2').hide();
                break;
            case "1":
            case "2":
                $par.find('#ts0').hide();
                $par.find('#ts1').show();
                $par.find('#ts3').hide();
                $par.find('#ts2').hide();
                break;
            case "3":
                $par.find('#ts0').hide();
                $par.find('#ts1').hide();
                $par.find('#ts3').show();
                $par.find('#ts2').hide();
                break;
            case "4":
                $par.find('#ts0').hide();
                $par.find('#ts1').hide();
                $par.find('#ts3').hide();
                $par.find('#ts2').show();
                break;
            default:
                break;
        }
    }
	
	setInterval(function() {
		saveForm(true);
	}, 2 * 60 * 1000);
	
	function saveForm(){
		
		var record = {};
		record["id"] = $("#form_id").val();
		//times
		record["times"] = timesToObject();
		record["general"] = generalToObject();
		record["location"] = locationToObject();
		record["cases"] = casesToObject();
		record["team"] = teamToObject();
		record["support"] = supportToObject();
		record["annuler"] = annulerToObject();
		record["dispatch"] = dispatchToObject();
		record["transport"] = transportToObject();
		var autoSave = ( arguments.length == 0 ) ? false : arguments[0];
		record["autoSave"] = autoSave;
		
		var formData = {
                form : record,
                "_token": "{{ csrf_token() }}"
            }
            
		$.ajax({
			url:'emergency_call',
			dataType:'json',
			type: 'post',
			data: formData,
			success:function(response){
				if(response.Status == 1){
					$('#form_id').val(response.Message);
					if( autoSave ) {
						$('#save_modal_text').text("Emergency Call auto-saved Succesfully");
						$('#save_modal').modal();
						setTimeout(function() {
							$('#save_modal').find('button')[0].click();
						}, 2000);
					} else {
						$('#save_modal_text').text("Emergency Call saved Succesfully");
						$('#save_modal').modal();
					}
				}else{
					if( response.Message != null ){
						$('#save_modal_text').text(response.Message);
					} else {
						$('#save_modal_text').text("Something went wrong, Consult IT department");
					}
					$('#save_modal').modal();
				}
			}
		});
		
		return record;
	}
	
	function timesToObject(){
		var times = {};
		times["call_time"] = moment($("#datetimepicker1").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
		times["dep_to_site"] = moment($("#datetimepicker2").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
		times["arrival_on_site"] = moment($("#datetimepicker3").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
		times["dep_to_dest"] = moment($("#datetimepicker4").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
		times["arrival_to_dest"] = moment($("#datetimepicker5").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
		times["unit_ready"] = moment($("#datetimepicker6").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
		times["return_to_station"] = moment($("#datetimepicker7").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
		return times;		
	}
	
	function generalToObject(){
		var general = {};
		general["type"] = ( $("#avp").prop("checked") ? "AVP" : ( $("#urgence").prop("checked") ? "URGENCE" : "TRANSPORT" ) );
		general["source"] = $("#source").val();
		general["source_phone"] = $("#source_phone").val();
		general["waiting"] = $("#waiting").val();
		general["cause_of_incident"] = $("#cause_of_incident").val();
		return general;
	}
	
	function locationToObject(){
		var location = {};
		location["from"] = {};
		location["from"]["type"] = $("#select_from").val();
		switch(location["from"]["type"]) {
			case "0":
				location["from"]["value"] = $(".from_specific_0").val();
				break;
			case "1":
			case "2":
				location["from"]["value"] = $(".from_specific_1").val();
				break;
			case "3":
				location["from"]["value"] = $(".from_specific_3").val();
				break;
			case "4":
				location["from"]["value"] = $(".from_specific_2").val();
				break;
			default:
				location["from"]["value"] = "";
		} 
		location["to"] = {};
		location["to"]["type"] = $("#select_to").val();
		switch(location["to"]["type"]) {
			case "0":
				location["to"]["value"] = $(".to_specific_0").val();
				break;
			case "1":
			case "2":
				location["to"]["value"] = $(".to_specific_1").val();
				break;
			case "3":
				location["to"]["value"] = $(".to_specific_3").val();
				break;
			case "4":
				location["to"]["value"] = $(".to_specific_2").val();
				break;
			default:
				location["to"]["value"] = "";
		}
		location["comment"] = $("#location_message").val();
		return location;		
	}
	
	function casesToObject(){
		var cases = {};
		cases["data"] = [];
		var counter=0;
		$cas = $(".patient_row");
		$.each( $cas , function ( key , val ) {
			
			var name = $(val).find('.case_name').val();
			var age = $(val).find('.case_age').val();
			var cas = $(val).find('.case_case').val();
			if(  ( name == null || name=="" ) && ( age==null || age=="" ) && ( cas==null || cas=="" ) ){
				//skpi me
			} else {
				cases["data"][counter] = {
					name : name,
					age : age,
					cas : cas
				};
				counter++;
			}
		});
		cases["count"] = counter;;
		return cases;
	}
	
	function teamToObject(){
		team = {};
		team["car_number"] = $("#car_number").val();
		team["driver"] = $("#driver").val();
		team["team_leader"] = $("#m_leader").val();
		team["emts"] = $("#emts").val();
		team["other_emts"] = $("#other_emts").val();
		return team;
	}
	
	function supportToObject(){
		support = {};
		support["anyway"] = {};
		if($("#anyway_switch")[0].checked) {
			support["anyway"]["enabled"] = true;
			support["anyway"]["time"] = moment($("#anyway_time").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
			support["anyway"]["responder"] = $("#anyway_resp").val();
		} else {
			support["anyway"]["enabled"] = false;			
		}
		support["dc"] = {};
		if($("#dc_switch")[0].checked) {
			support["dc"]["enabled"] = true;
			support["dc"]["time"] = moment($("#dc_time").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
			support["dc"]["responder"] = $("#dc_resp").val();
		} else {
			support["dc"]["enabled"] = false;			
		}
		support["ambulance"] = {};
		if($("#ambulance_switch")[0].checked) {
			support["ambulance"]["enabled"] = true;
			support["ambulance"]["time"] = moment($("#ambulance_time").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
			support["ambulance"]["responder"] = $("#ambulance_resp").val();
		} else {
			support["ambulance"]["enabled"] = false;			
		}
		support["sar"] = {};
		if($("#sar_switch")[0].checked) {
			support["sar"]["enabled"] = true;
			support["sar"]["time"] = moment($("#sar_time").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
			support["sar"]["responder"] = $("#sar_resp").val();
		} else {
			support["sar"]["enabled"] = false;			
		}
		support["info"] = $("#support_info").val();			
		return support;
	}
	
	function annulerToObject(){
		var annuler = {};
		if( $("#annuler_switch")[0].checked ){
			annuler["enabled"] = true;
			annuler["reason"] = $("#annuler_reason").val();
			annuler["source"] = $("#annuler_source").val();
			annuler["time"] = moment($("#annuler_time").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
		} else {
			annuler["enabled"] = false;
		}
		return annuler;
	}
	
	function dispatchToObject(){
		var dispatch = {};
		dispatch["dispatch_1"] = $("#dispatch_1").val();
		dispatch["dispatch_2"] = $("#dispatch_2").val();
		dispatch["or_1"] = $("#or_1").val();
		dispatch["or_2"] = $("#or_2").val();
		dispatch["leader"] = $("#dispatch_leader").val();
		dispatch["comment"] = $("#dispatch_comment").val();
		return dispatch;
	}
	
	function transportToObject(){
		var transport = {};
		if( $("#transport").prop("checked") ) {
			transport['enabled']=true;
			transport['transport_time'] = moment($("#transport_time").data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm');
			transport['transport_type'] = ( $("#hh").prop("checked") ? "hh" : ( $("#hd").prop("checked") ? "hd" : "dh" ) );
			transport['gamma'] = $("#gamma_switch")[0].checked;
			transport['admission'] = $('#admission').val();
			transport['doctor'] =$('#doctor').val();
			transport['reason_for_ambulance'] = $('#reason_for_ambulance').val();
			transport['cleared_ready'] = $('#cleared_ready').val();
			transport['o2'] = $("#o2_switch")[0].checked ? 1 : 0;
			transport['matelas'] = $("#matelas_switch")[0].checked ? 1 : 0;
			transport['couveuse'] = $("#couveuse_switch")[0].checked ? 1 : 0;
			transport['respirateur'] = $("#respirateur_switch")[0].checked ? 1 : 0;
			/*transport['o2'] = $('#o2').val();
			transport['matelas'] = $('#matelas').val();
			transport['couveuse'] = $('#couveuse').val();
			transport['respirateur'] = $('#respirateur').val();*/
		} else {
			transport['enabled']=false;
		}
		return transport;
	}

</script>

@endsection
