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

    .item5 .chosen-results {
	max-height:127px;
	}

.myImg {
  border-radius: 5px;
  cursor: pointer;
  transition: 0.3s;
}

.myImg:hover {opacity: 0.7;}

/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
}

/* Modal Content (image) */
.modal-content {
  margin: auto;
  display: block;
  width: 80%;
  max-width: 700px;
}

@-webkit-keyframes zoom {
  from {-webkit-transform:scale(0)} 
  to {-webkit-transform:scale(1)}
}

@keyframes zoom {
  from {transform:scale(0)} 
  to {transform:scale(1)}
}

/* The Close Button */
.close {
  position: absolute;
  top: 15px;
  right: 35px;
  color: #f1f1f1;
  font-size: 40px;
  font-weight: bold;
  transition: 0.3s;
}

.close:hover,
.close:focus {
  color: #bbb;
  text-decoration: none;
  cursor: pointer;
}

/* 100% Image Width on Smaller Screens */
@media only screen and (max-width: 700px){
  .modal-content {
    width: 100%;
  }
}

	div.checkable {
		cursor : pointer
	}


</style>

<link href="css/contextMenu/contextMenu.min.css" rel="stylesheet">

<div id="up_modal" class="modal">
	<div class="modal-dialog modal-sm">
		<div class="modal-content" style="width:110%">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span>
				</button>
				<h4 class="modal-title" id="myModalLabel2">Upload Documents</h4>
			</div>
			<div class="modal-body">
				<p id="save_modal_text">
					Open <a target="_blank" href="{{env('SURVEY_INTERNAL_URL')}}/up?id={{$record->id}}">{{env('SURVEY_INTERNAL_URL')}}/up?id={{$record->id}}</a>
				</p>
				<p>Or scan the below QrCode with your mobile</p>
				<img style="height:300px;" src="data:image/png;base64, {!! base64_encode(\QrCode::format('png')->size(400)->generate( env('SURVEY_INTERNAL_URL') . '/up?id=' . $record->id)) !!} ">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>{{ $page_title }}</h3>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 copy_me" id="1">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Team Information @if( $record->ml == Auth::user()->id )&nbsp;<a style="color:red" onclick="showUp()">Upload Documents</a>@endif
						@if( in_array(Auth::user()->id,$record->emts) || $record->ml == Auth::user()->id )&nbsp;<a style="color:green" onclick="showSurvey()"></a>@endif
					</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a onclick="hide_me(this)"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                    <div class="clearfix"></div>
                </div>
				@if( Auth::user() && Auth::user()->memberOf('Chef Equipe') )
                <div class="x_title">
                    <h2>Mission Checked Information&nbsp;
						@if( $record->checked == 1 )
						{{ $record->checked_by_user  }} @ {{ $record->checked_date  }}
						<span id="check_span" style='color:green;font-size:24px' class='glyphicon glyphicon-check' aria-hidden='true'></span>
						@else
						<span id="check_span" style='font-size:24px' class='glyphicon glyphicon-check' aria-hidden='true'></span>
						@endif
					</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a onclick="hide_me(this)"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                    <div class="clearfix"></div>
                </div>
				@endif
		<hr>
		@forelse($documents as $doc)
			@if( strpos( $doc->name ,'.pdf') )
				<a class="contextMenu" target="_blank" href="{{ Storage::url($doc->name) }}">
				<img class="myImg" id="pdf{{ $doc->id }}" src="/images/pdf.jpg" title="{{ explode("/",$doc->name)[3] }}" style="width:100%; max-width:100px;">
				</a>
			@elseif( strpos( $doc->name ,'.doc') || strpos( $doc->name ,'.docx'))
				<a class="contextMenu" target="_blank" href="{{ Storage::url($doc->name) }}">
				<img class="myImg" id="doc{{ $doc->id }}" src="/images/word.png" title="{{ explode("/",$doc->name)[3] }}" style="width:100%; max-width:100px;">
				</a>
			@elseif( strpos( $doc->name ,'.xls') || strpos( $doc->name ,'.xlsx'))
				<a class="contextMenu" target="_blank" href="{{ Storage::url($doc->name) }}">
				<img class="myImg" id="xls{{ $doc->id }}" src="/images/excel.png" title="{{ explode("/",$doc->name)[3] }}" style="width:100%; max-width:100px;">
				</a>
			@else
				<img class="myImg contextMenu" id="ttt{{ $doc->id }}" src="/images/spinner.gif" data-src="/image?name={{ $doc->name }}"style="width:100%; max-width:100px;">
			@endif
		@empty
			No Documents ]:
		@endforelse
		<hr>
                <form class="team_info_form">
		    <input type="text" style="display:none" id="urgence_id" value="{{ $record->id }}"/>
                    <div class="x_content">
                        <div class="well" style="overflow: auto">
<div>
                                <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Mission Type :&nbsp;</label>
                                <div class="col-md-3 col-sm-3 col-xs-12">
                                    <select name= "mission_type" id='mission_type_1' onchange='blabla(this)' data-placeholder="Choose the Mission Type..." class="select2_group form-control mission_type standardSelect" style="display:none;margin-bottom:20px;" tabindex="-1">
                                        <option value="-1"></option>
                                        @foreach($mission_subtypes as $mission_subtype)
                                        <optgroup label="{{ $mission_subtype->description }}">
                                            @foreach($mission_subtype->mission_types as $mission_type)
                                            <option @if($record->type == $mission_type->id) selected @endif value="{{ $mission_type->id }}">{{ $mission_type->name }}</option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                    <span id="mission_type_error" class="mission_type_error error"></span>
                                </div>
                            </div>
                        <hr width="100%">
                            <table id="add_emergency" >
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Car number :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <select name="car_number" data-placeholder="Choose an Ambulance..." class="form-control standardSelect" tabindex="-1" id="car_number" style="display:none;">
                                                    <option value="-1"></option>
                                                    @foreach($ambulances as $ambulance)
                                                    <option @if($record->ambulance_id == $ambulance->id) selected @endif value=" {{ $ambulance->id }} ">{{ $ambulance->number }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Driver :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <select class="form-control standardSelect" data-placeholder="Choose the Driver..."  id="driver" name="driver" tabindex="-1" style="display:none;">
                                                    <option value="-1"></option>
                                                    @foreach($drivers as $driver)
                                                    <option @if($record->driver == $driver->id) selected @endif value="{{ $driver->id }}">{{ $driver->nickname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span id="car_number_error" class="error"></span>
                                    </td>
                                    <td class="error_td">
                                        <span id="driver_error" class="error"></span>
                                    </td>
                                </tr>
                                <!--Row for emts-->
                                <tr style="vertical-align:top;">
                                    
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Mission Leader :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <select class="form-control standardSelect" id="m_leader" data-placeholder="Choose the Mission Leader..." name="m_leader" tabindex="-1" style="display:none;">
                                                    <option value="-1"></option>
                                                    @foreach($emts as $emt)
                                                    <option @if($record->ml == $emt->id) selected @endif value="{{ $emt->id }}">{{ $emt->nickname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >EMTs :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                        <select multiple id="emts" name="emt" data-placeholder="Choose all EMTs..." class="standardSelect" tabindex="-1" style="display: none;">                                                    <option value="-1"></option>
                                                    @foreach($emts as $emt)
                                                    <option @if(in_array($emt->id,$record->emts)) selected @endif value="{{ $emt->id }}">{{ $emt->nickname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span id="m_leader_error" class="error"></span>
                                    </td>
                                    <td class="error_td">
                                        <span id="emt_error" class="error"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Other EMTs (Other Station) :&nbsp;</label>
                                            <div class="col-md-9 xdisplay_inputx form-group has-feedback">
                                                <input value="{{$record->other_emts}}" title="Any EMT not present in the System" type="text" name="other_emts" class="other_emts" style="font-size:17px;width:100%">
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <!--Row for times-->
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Date :&nbsp;</label>
                                            <div class="col-md-9 xdisplay_inputx form-group has-feedback">
                                                <input type="text" class="form-control has-feedback-left" id="single_cal3" placeholder="Date" value=" {{ $date }} " name="date" aria-describedby="inputSuccess2Status3"/>
                                                <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                                <span id="inputSuccess2Status2" class="sr-only">(success)</span>
                                            </div>
                                        </div>
                                    </td>
                                     <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Call Time :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12" style="font-size:14px;">
                                                <input type="number" name="call_h" min=0 max=23 class="disable_me" value={{ explode(":",$record->call_time)[0] }} onchange="pad_me(this,'h_1')"/>
                                                <input type="number" name="call_m" min=0 max=59 class="disable_me" value={{ explode(":",$record->call_time)[1] }} onchange="pad_me(this,'m_1')"/>
                                                <span>&nbsp;&nbsp;(00:00 -> 23:59)</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span id="date_error" class="error"></span>
                                    </td>
                                    <td class="error_td">
                                        <span id="call_error" class="error"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Departue To Site :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12" style="font-size:14px;">
                                                <input type="number" name="dep_s_h" min=0 max=23 value={{ explode(":",$record->dep_to_site)[0] }} onchange="pad_me(this,'h_2')" class="h_1"/>
                                                <input type="number" name="dep_s_m" min=0 max=59 value={{ explode(":",$record->dep_to_site)[1] }} onchange="pad_me(this,'m_2')" class="m_1"/>
                                                <span>&nbsp;&nbsp;(00:00 -> 23:59)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Arrival On Site :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12" style="font-size:14px;">
                                                <input type="number" name="arr_s_h" min=0 max=23 class="disable_me h_1 h_2" value={{ explode(":",$record->arr_on_site)[0] }} onchange="pad_me(this,'h_3')"/>
                                                <input type="number" name="arr_s_m" min=0 max=59 class="disable_me m_1 m_2" value={{ explode(":",$record->arr_on_site)[1] }} onchange="pad_me(this,'m_3')"/>
                                                <span>&nbsp;&nbsp;(00:00 -> 23:59)</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span id="dep_s_error" class="error"></span>
                                    </td>
                                    <td class="error_td">
                                        <span id="arr_s_error" class="error"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Departure From Site :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12" style="font-size:14px;">
                                                <input type="number" name="dep_f_s_h" min=0 max=23 class="disable_me h_1 h_2 h_3" value={{ explode(":",$record->dep_from_site)[0] }} onchange="pad_me(this,'h_4')"/>
                                                <input type="number" name="dep_f_s_m" min=0 max=59 class="disable_me m_1 m_2 m_3" value={{ explode(":",$record->dep_from_site)[1] }} onchange="pad_me(this,'m_4')"/>
                                                <span>&nbsp;&nbsp;(00:00 -> 23:59)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Arrival To Destination :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12" style="font-size:14px;">
                                                <input type="number" name="arr_d_h" min=0 max=23 class="disable_me h_1 h_2 h_3 h_4" value={{ explode(":",$record->arr_to_dest)[0] }} onchange="pad_me(this,'h_5')"/>
                                                <input type="number" name="arr_d_m" min=0 max=59 class="disable_me m_1 m_2 m_3 m_4" value={{ explode(":",$record->arr_to_dest)[1] }} onchange="pad_me(this,'m_5')"/>
                                                <span>&nbsp;&nbsp;(00:00 -> 23:59)</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span id="dep_f_s_error" class="error"></span>
                                    </td>
                                    <td class="error_td">
                                        <span id="arr_d_error" class="error"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Unit Available :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12" style="font-size:14px;">
                                                <input class="h_1 h_2 h_3 h_4 h_5" type="number" name="unit_available_h" min=0 max=23 value={{ explode(":",$record->unit_available)[0] }} onchange="pad_me(this,'h_6')"/>
                                                <input class="m_1 m_2 m_3 m_4 m_5" type="number" name="unit_available_m" min=0 max=59 value={{ explode(":",$record->unit_available)[1] }} onchange="pad_me(this,'m_6')"/>
                                                <span>&nbsp;&nbsp;(00:00 -> 23:59)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Return To Station:&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12" style="font-size:14px;">
                                                <input type="number" name="ret_s_h" min=0 max=23 class="disable_me h_1 h_2 h_3 h_4 h_5 h_6" value={{ explode(":",$record->ret_to_centre)[0] }} onchange="pad_me(this,'')"/>
                                                <input type="number" name="ret_s_m" min=0 max=59 class="disable_me m_1 m_2 m_3 m_4 m_5 m_6" value={{ explode(":",$record->ret_to_centre)[1] }} onchange="pad_me(this,'')"/>
                                                <span>&nbsp;&nbsp;(00:00 -> 23:59)</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span id="unit_available_error" class="error"></span>
                                    </td>
                                    <td class="error_td">
                                        <span id="ret_s_error" class="error"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="x_title">
                        <h2>Patient Information</h2>
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <div class="well" style="overflow: auto">
                            
                            <table>
                                <tr>
                                    <td>
                                        <div class="form-group" >
                                            <label class="control-label col-md-3 col-sm-3 col-xs-3">From &nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-9">
                                                <select data-placeholder="Choose where from..." class="form-control from standardSelect" name="from" onchange="change_from(this)" tabindex="-1" style="display:none;">
                                                    <option @if($record->from == "") selected @endif value="-1"></option>
                                                    <option @if(explode('.',$record->from)[0] == 0) selected @endif value="0">Hospital</option>
                                                    <option @if(explode('.',$record->from)[0] == 1) selected @endif value="1">House</option>
                                                    <option @if(explode('.',$record->from)[0] == 2) selected @endif value="2">Road</option>
                                                    <option @if(explode('.',$record->from)[0] == 3) selected @endif value="3">Station</option>
                                                    <option @if(explode('.',$record->from)[0] == 4) selected @endif value="4">Others</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="width:300px;">
                                        <div class="form-group" >
                                            <span id="fs0" @if(explode('.',$record->from)[0] != 0) style="display:none;" @endif>
                        <select dropup class="form-control from_specific_0 standardSelect" data-placeholder="Choose a hospital..." name="from_specific_0" tabindex="-1" style="width:240px;display:none">
                                                <option value="-1"></option>
                                                @foreach($hospitals as $hospital)
                                                <option @if(explode('.',$record->from)[0] == 0 and explode('.',$record->from)[1] == $hospital->id) selected @endif value="{{ $hospital->id }}">{{ $hospital->code . " (" . $hospital->name . ")" }}</option>
                                                @endforeach
                                            </select>
                        </span>
                                            <span id="fs1" @if(explode('.',$record->from)[0] != 1 and explode('.',$record->from)[0] != 2) style="display:none;" @endif>
                                            <select dropup class="form-control from_specific_1 standardSelect" name="from_specific_1" data-placeholder="Choose a location..." style="width:240px;display:none" tabindex="-1">
                                                <option value="-1"></option>
                                                @foreach($locations as $location)
                                                <option @if( ( explode('.',$record->from)[0] == 1 or explode('.',$record->from)[0] == 2 ) and explode('.',$record->from)[1] == $location->id) selected @endif value = " {{ $location->id }} ">{{ $location->village . " (" . $location->station . ")" }}</option>
                                                @endforeach
                                            </select>
                        </span>
                                            <span id="fs3" @if(explode('.',$record->from)[0] != 3) style="display:none;" @endif>
                                            <select dropup class="form-control from_specific_3 standardSelect" name="from_specific_3" style="width:240px;display:none" tabindex="-1" data-placeholder="Choose a Station...">
                                                <option value="-1"></option>
                                                @foreach($stations as $station)
                                                <option @if(explode('.',$record->from)[0] == 3 and explode('.',$record->from)[1] == $station->id) selected @endif value = " {{ $station->id }} ">{{ $station->id . " (" . $station->name . ")"  }}</option>
                                                @endforeach
                                            </select>
                        </span>
                        <span id="fs2" @if(explode('.',$record->from)[0] != 4) style="display:none;" @endif>
                                            <input type="text" maxlength="100" class="form-control from_specific_2" name="from_specific_2" placeholder="From ... " style="width:240px;" @if(explode('.',$record->from)[0] == 4) value="{{ $record->from_other }}" @endif  />
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
                                    <td>
                                        <div class="form-group" >
                                            <label class="control-label col-md-3 col-sm-3 col-xs-3">To   &nbsp;&nbsp;&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-9">
                                                <select data-placeholder="Choose where to..." class="form-control to standardSelect" name="to" onchange="change_to(this)"  tabindex="-1" style="display:none;">
                                                    <option @if($record->to == "") selected @endif value="-1"></option>
                                                    <option @if(explode('.',$record->to)[0] == 0) selected @endif value="0">Hospital</option>
                                                    <option @if(explode('.',$record->to)[0] == 1) selected @endif value="1">House</option>
                                                    <option @if(explode('.',$record->to)[0] == 2) selected @endif value="2">Road</option>
                                                    <option @if(explode('.',$record->to)[0] == 3) selected @endif value="3">Station</option>
                                                    <option @if(explode('.',$record->to)[0] == 4) selected @endif value="4">Others</option>
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group" >
                                            <span id="ts0" @if(explode('.',$record->to)[0] != 0) style="display:none;" @endif>
                         <select class="form-control to_specific_0 standardSelect" name="to_specific_0" data-placeholder="Choose a hospital..." style="width:240px;display:none" tabindex="-1">
                                                <option value="-1"></option>
                                                @foreach($hospitals as $hospital)
                                                <option @if(explode('.',$record->to)[0] == 0 and explode('.',$record->to)[1] == $hospital->id) selected @endif value="{{ $hospital->id }}">{{ $hospital->code . " (" . $hospital->name . ")" }}</option>
                                                @endforeach
                                            </select>
                         </span>
                                            <span id="ts1" @if( explode('.',$record->to)[0] != 1 and explode('.',$record->to)[0] != 2) style="display:none;" @endif>
                                            <select class="form-control to_specific_1 standardSelect" name="to_specific_1" data-placeholder="Choose a location..." style="width:240px;display:none" tabindex="-1">
                                                <option value="-1"></option>
                                                @foreach($locations as $location)
                                                <option @if( ( explode('.',$record->to)[0] == 1 || explode('.',$record->to)[0] == 2 ) and explode('.',$record->to)[1] == $location->id) selected @endif value = " {{ $location->id }} ">{{ $location->village . " (" . $location->station . ")" }}</option>
                                                @endforeach
                                            </select>
                         </span>
                                            <span id="ts3" @if(explode('.',$record->to)[0] != 3) style="display:none;" @endif>
                                            <select class="form-control to_specific_3 standardSelect" name="to_specific_3" data-placeholder="Choose a Station..." style="width:240px;display:none" tabindex="-1">
                                                <option value="-1"></option>
                                                @foreach($stations as $station)
                                                <option @if(explode('.',$record->to)[0] == 3 and explode('.',$record->to)[1] == $station->id) selected @endif value = " {{ $station->id }} ">{{ $station->id . " (" . $station->name . ")"  }}</option>
                                                @endforeach
                                            </select>
                         </span>
                                            <span id="ts2" @if(explode('.',$record->to)[0] != 4) style="display:none;" @endif>
                                            <input type="text" maxlength="100" class="form-control to_specific_2" name="to_specific_2" placeholder="To ... " style="width:240px;" @if(explode('.',$record->to)[0] == 4) value="{{ $record->to_Other }}" @endif />
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
                            <hr width="100%">
                            <table>
                                <tr><!--Annule-->
                                    <td>
                                        <div class="checkbox">
                                            <label>
                                                <input onchange="blablabla(this)" @if($record->annuler) checked @endif type="checkbox" name="annuler" class="annuler flat"> No Patient (Annulé / Post / Moukhtalaf)
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr><!--Name-->
                                    <td>
                                        <span>First Name :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <input @if($record->annuler == false) value="{{ explode('%%%',$record->patient_name)[0] }}" @endif type="text" class="form-control has-feedback-left first_name" name="fname" id="first_name" placeholder="First Name">
                                            <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span>Last Name :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <input @if($record->annuler == false) value="{{ explode('%%%',$record->patient_name)[1] }}" @endif type="text" class="form-control has-feedback-left last_name" id="last_name" name="lname" placeholder="Last Name">
                                            <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span>Phone Number :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <input @if($record->annuler == false) value="{{$record->phone}}" @endif type="text" maxlength="20" class="form-control has-feedback-left phone_number" id="phone_number" name="phone_number" placeholder="Phone Number">
                                            <span class="fa fa-phone form-control-feedback left" aria-hidden="true"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr><!--Name Error-->
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span id="first_name_error" class="first_name_error error"></span>
                                    </td>
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span id ="last_name_error" class="last_name_error error"></span>
                                    </td>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr><!--Birth-->
                                    <td>
                                        <span>Age :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <input @if($record->annuler == false) value="{{ $record->patient_age }}" @endif type="number" min="0" max="200" class="form-control has-feedback-left year" name="year" id="year" placeholder="Age">
                                            <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span>Sex :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <select name="sex" class="form-control sex standardSelect" id="sex" tabindex="-1" style="display:none;">
                                                <option @if($record->annuler == false and $record->patient_sex == "Male") selected  @endif value="1">Male</option>
                                                <option @if($record->annuler == false and $record->patient_sex == "Female") selected  @endif value="2">Female</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <span>Nationality :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <select class="form-control from_specific_0 nationality standardSelect" name="nationality" tabindex="-1" style="display:none;">
                                                <option value="-1"></option>
                                                @foreach($nationalities as $nationality)
                                                <option @if($record->annuler == false and $record->patient_nationality == $nationality->id) selected  @endif value="{{ $nationality->id }}">{{ $nationality->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr><!--Birth Error-->
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span id="year_error" class="year_error error"></span>
                                    </td>
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span id="sex_error" class="sex_error error"></span>
                                    </td>
                                    <td></td>
                                    <td class="error_td">
                                        <span id="nationality_error" class="nationality_error error"></span>
                                    </td>
                                </tr>
                            </table>
                            <hr width="100%">
                            <table>
                                <tr>
                                    <td>
                                        <span>
                                            Chief Complaint :&nbsp;
                                        </span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <input @if($record->annuler == false) value="{{ $record->patient_case }}" @endif type="text" maxlength="100" class="form-control complaint" id="chiefComplaint" name="chief_complaint" placeholder="Chief Complaint" style="font-size:17px;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span id="complaint_error" class="complaint_error error"></span>
                                    </td>
                                </tr>
                                <!--<tr>
                                    <td>
                                        <span>
                                            Material Used :&nbsp;
                                        </span>
                                    </td>
                                    <td colspan="2">
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <textarea name="material" class="material" style="width:358px;height:60px;">@if($record->annuler == false) {{ $record->material_used }} @endif</textarea>
                                        </div>
                                    </td>
                                </tr>-->

                                <tr>
                                    <td>
                                        <span>
                                            Material Used :&nbsp;
                                        </span>
                                    </td>
                                    <td colspan="2">
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group item_5">
                                            <select size="5" multiple class="form-control from_specific_0 material_2 standardSelect" name="material_2" tabindex="-1" style="max-height:127px;display:none;width:150px;">
                                                <option value="-1"></option>
                                                @foreach($material as $m)
                                                <option @if( in_array($m->id , $record->material) ) selected @endif value="{{ $m->id }}">{{ $m->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span id="material_error" class="material_error error"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span>
                                            O<sub>2</sub> :&nbsp;
                                        </span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <input @if($record->annuler == false) value="{{ $record->o2 }}" @endif type="text" name="o2" class="o2" style="font-size:17px;width:100%"/>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span>
                                            Comment :&nbsp;
                                        </span>
                                    </td>
                                    <td colspan="2">
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <textarea  name="comment" class="comment" style="width:358px;height:60px;font-size:17px;">{{ $record->comment }}</textarea>
                                        </div>
                                    </td>
                                </tr>

                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel" id="bottom_panel">
                <div class="x_title">
                    <h2>Actions</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="well" style="overflow: auto">
                        <button type="button" class="btn btn-success btn-lg" onclick="submit()">Submit</button>
						@if( Auth::user() && Auth::user()->memberOf('Chef Equipe') ) 
						<button id="check_button" type="button" class="btn btn-danger btn-lg" onclick="check()">Team Leader Check</button>	
						@endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="myModal" class="modal">
  <span class="close">&times;</span>
  <img class="modal-content" id="img01">
</div>

<script src="js/contextMenu/contextMenu.min.js"></script>
<script src="js/contextMenu/position.min.js"></script>

<script>
    (function ($) {
        $.fn.serlizeFromJSON = function (){
            
            var a = this.serializeArray();

            var o = {};
            $.each(a,function (){
                if(o[this.name]){
                    if(!o[this.name].push){
                        o[this.name] = [o[this.name]];                    
                    }
                        o[this.name].push(this.value || '');
                    }else{
                        o[this.name] = this.value || '';
                    }
                });
                return o;
        }
    })(jQuery);

var modal = document.getElementById('myModal');
var modalImg = document.getElementById("img01");

@foreach($documents as $doc)
@if( !strpos( $doc->name ,'.pdf') 
		&& 
	!strpos( $doc->name ,'.doc') 
		&& 
	!strpos( $doc->name ,'.docx') 
		&& 
	!strpos( $doc->name ,'.xls') 
		&& 
	!strpos( $doc->name ,'.xlsx') )
var img{{$doc->id}} = document.getElementById('ttt{{ $doc->id }}');
img{{$doc->id}}.onclick = function(){
  modal.style.display = "block";
  modalImg.src = this.src;
}
@endif
@endforeach

/*$( ".contextMenu" ).contextmenu(function(event) {
  event.preventDefault();
  alert( event.X );
});*/

$.contextMenu({
    selector: '.contextMenu',
        items: {
            key: {
                name: "Delete", 
		icon: "fa-remove",
                callback: function(key, opt){ 
				if ( opt.$trigger[0].nodeName === 'IMG' ) {
					deleteImage(opt.$trigger[0].id); 
				} else {
					deleteImage(opt.$trigger[0].lastElementChild.id); 
				}
                            
                        }
            }
        }
});

function deleteImage(idStr) {

var id = idStr.substring(3);

var formData = {
                form : {
			doc_id : id,
			urg_id : $("#urgence_id").val()
		},
                "_token": "{{ csrf_token() }}"
            };


$.ajax({
                url:'unlink_emergency_document',
                dataType:'json',
                type: 'post',
                data: formData,
                success:function(response){
                    if(response.Status == 1){
                        alert(response.Message);
			hideImage(idStr);
                    }else if(response.Status == 0){
			alert(response.Message);
		    }else{ 
                        alert("Something went wrong, Consult IT department");
                    }
                }
            });


}

function hideImage(idStr) {

	$element = $('#' + idStr);
	if ( $element.hasClass( "contextMenu" ) ) {
		$element.hide();
	} else {
		$element.parent().hide();
	}
}

var span = document.getElementsByClassName("close")[0];
span.onclick = function() { 
  modal.style.display = "none";
}

    function blabla(e){
        $f = $(e).parent().parent().parent().parent().parent();
        var selectedId = $f.find('#mission_type_1').val();
        if(selectedId == '13'){
            $f.find("#car_number").prop('disabled', true).trigger("chosen:updated");
            $f.find("#driver").prop('disabled', true).trigger("chosen:updated");
	    $f.find(".disable_me").prop('disabled', true);
        }else{
            $f.find("#car_number").prop('disabled', false).trigger("chosen:updated");
            $f.find("#driver").prop('disabled', false).trigger("chosen:updated");
	    $f.find(".disable_me").prop('disabled', false);
        }
    }

    function blablabla(e){

        var selectedId = $(e).is(':checked');
        if(selectedId){
            $(e).parent().parent().parent().parent().parent().find('.first_name').first().prop('disabled', true);
            $(e).parent().parent().parent().parent().parent().find(".year").prop('disabled', true);
            $(e).parent().parent().parent().parent().parent().find(".last_name").prop('disabled', true);
            $(e).parent().parent().parent().parent().parent().find(".sex").prop('disabled', true).trigger("chosen:updated");
            $(e).parent().parent().parent().parent().parent().find(".nationality").prop('disabled', true).trigger("chosen:updated");
            $(e).parent().parent().parent().parent().parent().parent().parent().find(".complaint").prop('disabled', true);
            $(e).parent().parent().parent().parent().parent().parent().parent().find(".material").prop('disabled', true);
            $(e).parent().parent().parent().parent().parent().parent().parent().find(".material_2").prop('disabled', true).trigger("chosen:updated");
            $(e).parent().parent().parent().parent().parent().parent().parent().find(".o2").prop('disabled', true);
        }else{
            $(e).parent().parent().parent().parent().parent().find(".first_name").prop('disabled', false);
            $(e).parent().parent().parent().parent().parent().find(".year").prop('disabled', false);
            $(e).parent().parent().parent().parent().parent().find(".last_name").prop('disabled', false);
            $(e).parent().parent().parent().parent().parent().find(".sex").prop('disabled', false).trigger("chosen:updated");
            $(e).parent().parent().parent().parent().parent().find(".nationality").prop('disabled', false).trigger("chosen:updated");
            $(e).parent().parent().parent().parent().parent().parent().parent().find(".complaint").prop('disabled', false);
            $(e).parent().parent().parent().parent().parent().parent().parent().find(".material").prop('disabled', false);
            $(e).parent().parent().parent().parent().parent().parent().parent().find(".material_2").prop('disabled', false).trigger("chosen:updated");
            $(e).parent().parent().parent().parent().parent().parent().parent().find(".o2").prop('disabled', false);
        }

    }

    $(document).ready(function(){

        if({{ $record->annuler ? 'true' : 'false' }}) {
            blablabla($('.annuler')[0]);
        }

       /* $(".standardSelect").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%",
            disabled: true
        });*/
	$(".standardSelect").select2({ width: "100%" });
    blabla($('#mission_type_1'));
        $('.item_5').find('.chosen-results').css('max-height','127px');

	var allImages = $('img');
	allImages.each( function() {

		if( $(this).attr('data-src') ) {
	
			$(this).attr('src',$(this).attr('data-src'));
		}
	});

    });

    function isset(variable){
        if(typeof(variable) != "undefined" && variable !== null){
            return true;
        }
        return false;
    }

    function containsDuplicates(array) {
        var c1 = new Set(array).size;
        var c2 = array.length;
        if(c1 == c2){
            return false;
        }
        return true;
    }

    function submit(){

        $('.error').text('');
        var flag = true;

        var i = 0;
        var obj = {};
        var json_data = $('.team_info_form').serlizeFromJSON();
        json_data.id = $("#urgence_id").val();
    obj = json_data;

            //Check Car
            if(json_data.car_number == -1 && json_data.mission_type != 13){
                $('#car_number_error').text('You must select an ambulance');
                flag = false;
            }
            var emts = [];
            //Check Driver
            if(json_data.driver == -1 && json_data.mission_type != 13){
                $('#driver_error').text('You must select a driver');
                flag = false;
            } 
            else {
                emts.push(json_data.driver);
            }
            //Mission Leader
            /*if(json_data.m_leader == -1){
                $('#m_leader_error').text('You must select a mission leader');
                flag = false;
            }else{
            }*/ //Off For Now//
            if(json_data.m_leader != -1)
                emts.push(json_data.m_leader);
            if(typeof json_data.emt != 'undefined')
		emts = emts.concat(json_data.emt);
            if(containsDuplicates(emts)){
                $('#emt_error').text('A name is selected twice');
                flag = false;
            }
	    
            //Date never wrong ??
            //Time as well??
            //Mission Type
            if(json_data.mission_type == -1){
                $('#mission_type_error').text('You must select a mission type');
                flag = false;
            }
            var annuler = isset(json_data.annuler);      
            if(!annuler){
                //Patient name
                if(json_data.fname.trim() == ""){
                    $('#first_name_error').text('You must add a first name');
                    flag = false;
                }
                if(json_data.lname.trim() == ""){
                    $('#last_name_error').text('You must add a last name');
                    flag = false;
                }
                if(json_data.year.trim() == ""){
                    $('#year_error').text('You must select a year of birth');
                    flag = false;
                }
                if(json_data.nationality == -1){
                    $('#nationality_error').text('You must select a Nationality');
                    flag = false;
                }
                if(json_data.chief_complaint == ""){
                    $('#complaint_error').text('You must add a chief complaint');
                    flag = false;
                }
              //  if(json_data.material == ""){
              //      $('#material_error').text('You must add material used');
              //      flag = false;
               // }
            }
            if(json_data.from == -1){
                $('#from_error').text('You must choose a location');
                flag = false;
            }else{
                switch (json_data.from) {
                    case "0":
                        json_data.from_specific = json_data.from_specific_0.trim();
                        break;
                    case "1":
                    case "2":
                        json_data.from_specific = json_data.from_specific_1.trim();
                        break;
                    case "4":                    
                        json_data.from_specific = json_data.from_specific_2.trim();
                        break;
                    case "3":
                        json_data.from_specific =  json_data.from_specific_3.trim();
                        break;
                    default:
                        json_data.from_specific = "";
                        break;
                }
                if(json_data.from_specific == "" || json_data.from_specific == "-1"){
                    $('#from_error').text('You must choose a location');
                    flag = false;
                }
            }
            if(json_data.to == -1){
                $('#to_error').text('You must choose a location');
                flag = false;
            }else{
                switch (json_data.to) {
                    case "0":
                        json_data.to_specific =  json_data.to_specific_0.trim();
                        break;
                    case "1":
                    case "2":
                        json_data.to_specific =  json_data.to_specific_1.trim();
                        break;
                    case "4":                    
                        json_data.to_specific =  json_data.to_specific_2.trim();
                        break;
                    case "3":
                        json_data.to_specific =  json_data.to_specific_3.trim();
                        break;
                    default:
                        json_data.to_specific = "";
                        break;
                }
                if(json_data.to_specific == "" || json_data.to_specific == "-1"){
                    $('#to_error').text('You must choose a location');
                    flag = false;
                }
        }

        if(flag){
            var formData = {
                form : JSON.stringify(obj),
                "_token": "{{ csrf_token() }}"
            }
            
            $.ajax({
                url:'edit_emergency',
                dataType:'json',
                type: 'post',
                data: formData,
                success:function(response){
                    if(response.Status == 1){
                        alert("Emergency Edited Succesfully");
                        window.location.href="report";
                    }else if(response.Status == 0){
			alert(response.Message);
		    }else{ 
                        alert("Something went wrong, Consult IT department");
                    }
                }
            });
        }else {
            alert('Check Error Messages');
        }

    }

    function reset(){

        $('.team_info_form').trigger('reset');
    }

    function hide_me(e){
        $par = $(e).parent().parent().parent().parent();
        $con = $par.find('.team_info_form').first();
        if($con.is(":visible")){
            $con.hide();
            $(e).find('i').toggleClass('fa-chevron-down');
            $(e).find('i').toggleClass('fa-chevron-up');
        }else{
            $con.show();
            $(e).find('i').toggleClass('fa-chevron-down');
            $(e).find('i').toggleClass('fa-chevron-up');
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

    function pad_me(e,name){
        $val =  $(e).val();
        if($val.length == 1){
            $val = "0" + $val;
            $(e).val($val);
        }

        $('.' + name).val($val);
    }
	
	function showUp(){
		$('#up_modal').modal();
		$('.modal-backdrop').css('opacity',0);
		$('.modal-backdrop').hide();
	}
	
	function check() {
		var formData = {
			id : $("#urgence_id").val(),
			"_token": "{{ csrf_token() }}"                        
		}

		$.ajax({
			url:'check_emergency',
			dataType:'json',
			type: 'post',
			data: formData,
			success:function(response){
				if(response.Status == 1){
					$('#check_button').prop('disabled',true);
					$('#check_span').css('color','green');
				}else {
					alert(response.Message);
				}
			}
		});
	}

</script>
<!-- YAMLI CODE START -->
<script type="text/javascript" src="https://api.yamli.com/js/yamli_api.js"></script>
<script type="text/javascript">
  if (typeof(Yamli) == "object" && Yamli.init( { uiLanguage: "en" , startMode: "onOrUserDefault" } ))
  {
    Yamli.yamlify( "chiefComplaint", { settingsPlacement: "inside" } );
    Yamli.yamlify( "comment", { settingsPlacement: "inside" } );

  }
</script>
<!-- YAMLI CODE END -->
@endsection
