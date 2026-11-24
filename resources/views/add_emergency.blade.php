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

    div .chosen-container {
    padding-bottom:5px;
    }

    .item5 .chosen-results {
	max-height:127px;
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
        <div class="col-md-12 col-sm-12 col-xs-12 copy_me" id="1">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Team Information</h2>
                        <ul class="nav navbar-right panel_toolbox">
                            <li><a onclick="hide_me(this)"><i class="fa fa-chevron-up"></i></a>
                            </li>
                        </ul>
                    <div class="clearfix"></div>
                </div>
                <form class="team_info_form">
                    <div class="x_content">
                        <div class="well" style="overflow: auto">
                <input type="text" style="display:none" id="parent_id" value="{{ $record != null ? $record->parent_id : "" }}"/>
                            <div>
                                <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Mission Type :&nbsp;</label>
                                <div class="col-md-3 col-sm-3 col-xs-12" >
                                    <select name= "mission_type" id='mission_type_1' onchange='blabla(this)' data-placeholder="Choose the Mission Type..." class="select2_group form-control mission_type standardSelect" style="display:none;margin-bottom:20px;" tabindex="-1">
                                        <option value="-1"></option>
                                        @foreach($mission_subtypes as $mission_subtype)
                                        <optgroup label="{{ $mission_subtype->description }}">
                                            @foreach($mission_subtype->mission_types as $mission_type)
                                            <option value="{{ $mission_type->id }}" @if($record != null && $record->type == $mission_type->id) selected @endif>{{ $mission_type->name }}</option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                    <span id="mission_type_error" class="error"></span>
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
                                                    <option value="-1" selected></option>
                                                    @foreach($ambulances as $ambulance)
                                                    <option value="{{ $ambulance->id }}" @if($record != null && $record->ambulance_id == $ambulance->id) selected @endif>{{ $ambulance->number }}</option>
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
                                                    <option value="{{ $driver->id }}" @if($record != null && $record->driver == $driver->id) selected @endif>{{ $driver->nickname }}</option>
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
                                                    <option value="{{ $emt->id }}" @if($record != null && $record->ml == $emt->id) selected @endif>{{ $emt->nickname }}</option>
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
                                                    <option value="{{ $emt->id }}" @if($record != null && in_array($emt->id,$record->emts) ) selected @endif>{{ $emt->nickname }}</option>
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
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Other EMTs (Other Stations):&nbsp;</label>
                                            <div class="col-md-9 xdisplay_inputx form-group has-feedback">
                                                <input title="Any EMT not present in the System" type="text" name="other_emts" class="other_emts" style="font-size:17px;width:100%" @if($record!= null)value="{{ $record->other_emts }}" @endif>
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
                                                <input type="number" class="disable_me hour" name="call_h" min=0 max=23 @if($record != null) value={{ explode(":",$record->call_time)[0] }} @else value=00 @endif onchange="pad_me(this,'h_one')"/>
                                                <input type="number" class="disable_me minute" name="call_m" min=0 max=59 @if($record != null) value={{ explode(":",$record->call_time)[1] }} @else value=00 @endif onchange="pad_me(this,'m_one')"/>
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
                                                <input type="number" class="h_one hour" name="dep_s_h" min=0 max=23 @if($record != null) value={{ explode(":",$record->dep_to_site)[0] }} @else value=00 @endif onchange="pad_me(this,'h_two')"/>
                                                <input type="number" class="m_one minute" name="dep_s_m" min=0 max=59 @if($record != null) value={{ explode(":",$record->dep_to_site)[1] }} @else value=00 @endif onchange="pad_me(this,'m_two')"/>
                                                <span>&nbsp;&nbsp;(00:00 -> 23:59)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Arrival On Site :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12" style="font-size:14px;">
                                                <input type="number" class="h_one h_two disable_me hour" name="arr_s_h" min=0 max=23 @if($record != null) value={{ explode(":",$record->arr_on_site)[0] }} @else value=00 @endif onchange="pad_me(this,'h_three')"/>
                                                <input type="number" class="m_one m_two disable_me minute" name="arr_s_m" min=0 max=59 @if($record != null) value={{ explode(":",$record->arr_on_site)[1] }} @else value=00 @endif onchange="pad_me(this,'m_three')"/>
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
                                                <input type="number" class="h_one h_two h_three disable_me hour" name="dep_f_s_h" min=0 max=23 @if($record != null) value={{ explode(":",$record->dep_from_site)[0] }} @else value=00 @endif onchange="pad_me(this,'h_four')"/>
                                                <input type="number" class="m_one m_two m_three disable_me minute" name="dep_f_s_m" min=0 max=59 @if($record != null) value={{ explode(":",$record->dep_from_site)[1] }} @else value=00 @endif onchange="pad_me(this,'m_four')"/>
                                                <span>&nbsp;&nbsp;(00:00 -> 23:59)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Arrival To Destination :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12" style="font-size:14px;">
                                                <input type="number" class="h_one h_two h_three h_four disable_me hour" name="arr_d_h" min=0 max=23 @if($record != null) value={{ explode(":",$record->arr_to_dest)[0] }} @else value=00 @endif onchange="pad_me(this,'h_five')"/>
                                                <input type="number" class="m_one m_two m_three m_four disable_me minute" name="arr_d_m" min=0 max=59 @if($record != null) value={{ explode(":",$record->arr_to_dest)[1] }} @else value=00 @endif onchange="pad_me(this,'m_five')"/>
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
                                                <input type="number" class="h_one h_two h_three h_four h_five hour" name="unit_available_h" min=0 max=23 @if($record != null) value={{ explode(":",$record->unit_available)[0] }} @else value=00 @endif onchange="pad_me(this,'h_six')"/>
                                                <input type="number" class="m_one m_two m_three m_four m_five minute" name="unit_available_m" min=0 max=59 @if($record != null) value={{ explode(":",$record->unit_available)[1] }} @else value=00 @endif onchange="pad_me(this,'m_six')"/>
                                                <span>&nbsp;&nbsp;(00:00 -> 23:59)</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Return To Station:&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12" style="font-size:14px;">
                                                <input type="number" class="h_one h_two h_three h_four h_five h_six disable_me hour" name="ret_s_h" min=0 max=23 @if($record != null) value={{ explode(":",$record->ret_to_centre)[0] }} @else value=00 @endif onchange="pad_me(this,'')"/>
                                                <input type="number" class="m_one m_two m_three m_four m_five m_six disable_me minute" name="ret_s_m" min=0 max=59 @if($record != null) value={{ explode(":",$record->ret_to_centre)[1] }} @else value=00 @endif onchange="pad_me(this,'')"/>
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
                                            <input type="text" maxlength="100" class="form-control from_specific_2" name="from_specific_2" placeholder="From ... " style="width:240px;">
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
                                            <input type="text" maxlength="100" class="form-control to_specific_2" name="to_specific_2" placeholder="To ... " style="width:240px;">
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
                                                <input onchange="blablabla(this)" type="checkbox" id="annuler" name="annuler" class="annuler flat"> No Patient (Annule / Post / Moukhtalaf)
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
                                            <input type="text" style="text-transform: capitalize;" class="form-control has-feedback-left first_name" name="fname" id="first_name" placeholder="First Name">
                                            <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span>Last Name :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <input type="text" style="text-transform: capitalize;" class="form-control has-feedback-left last_name" id="last_name" name="lname" placeholder="Last Name">
                                            <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span>Phone Number :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <input type="text" maxlength="20" class="form-control has-feedback-left phone_number" id="phone_number" name="phone_number" placeholder="Phone Number">
                                            <span class="fa fa-phone form-control-feedback left" aria-hidden="true"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr><!--Name Error-->
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span id ="first_name_error" class="error"></span>
                                    </td>
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span id="last_name_error" class="error"></span>
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
                                            <input type="number" min="0" max="200" class="form-control has-feedback-left year" name="year" id="year" placeholder="Age">
                                            <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span>Sex :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <select name="sex" class="form-control sex standardSelect" id="sex" tabindex="-1" style="display:none;">
                                                <option value="1">Male</option>
                                                <option value="2" selected>Female</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <span>Nationality :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div style="width:300px;" class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <select class="form-control from_specific_0 nationality standardSelect" name="nationality" tabindex="-1" style="display:none;width:150px;">
                                                <option value="-1"></option>
                                                @foreach($nationalities as $nationality)
                                                <option value="{{ $nationality->id }}">{{ $nationality->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr><!--Birth Error-->
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span class="error" id="year_error"></span>
                                    </td>
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span class="sex_error error" id="sex_error"></span>
                                    </td>
                                    <td></td>
                                    <td class="error_td">
                                        <span class="nationality_error error" id="nationality_error"></span>
                                    </td>
                                </tr>
                            </table>
                            <hr width="100%">
                            <table>
                                <!--<tr>
                                    <td>
                                        <span>
                                            Material Used :&nbsp;
                                        </span>
                                    </td>
                                    <td colspan="2">
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <textarea name="material" class="material" style="width:358px;height:60px;"></textarea>
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
                                                <option value="{{ $m->id }}">{{ $m->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span class="material_error error" id="material_error"></span>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <span>
                                            Chief Complaint :&nbsp;
                                        </span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <input type="text" maxlength="100" class="form-control complaint" name="chief_complaint" placeholder="Chief Complaint" id="chiefComplaint" style="text-transform: capitalize; font-size:17px;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span class="complaint_error error" id="complaint_error"></span>
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
                                            <input type="text" name="o2" class="o2" style="font-size:17px;width:100%"/>
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
                                            <textarea name="comment" class="comment" style="width:358px;height:60px;font-size:17px;"></textarea>
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
                        <button type="button" class="btn btn-success btn-lg" onclick="reset()">Reset</button>
                        <button type="button" class="btn btn-success btn-lg" onclick="submit()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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


	if(selectedId == '14'){
		$('#annuler').prop("checked", true);
	}else{
		$('#annuler').prop("checked", false);
	}
	blablabla($('#annuler'));
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
        /*$(".standardSelect").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%",
            disabled: true
        });*/
	$(".standardSelect").select2({ width: "100%" });



        $('.item_5').find('.chosen-results').css('max-height','127px');
		
		
		var fromCall = getUrlParameter('from_call');
		if( fromCall!=undefined ){
			
			var formData = {
				callId : fromCall,
				"_token": "{{ csrf_token() }}"
			}
            
			$.ajax({
				url:'emergency_call_data',
				dataType:'json',
				type: 'post',
				data: formData,
				success:function(response){
					if(response.Status == 1){
						loadFromCall(JSON.parse(response.Data));
					}else{
						//alert("Something went wrong, Consult IT department");
					}
				}
			});
		}
    });
	
	function loadFromCall(record){
		
		$("#car_number").val(record["car_number"]).trigger("change");
		$("#driver").val(record["driver"]).trigger("change");
		$("#m_leader").val(record["team_leader"]).trigger("change");
		$("#emts").val(record["urg_emts"]).trigger("change");
		$(".other_emts").val(record["other_emts"]);
		
		if( record['call_time']!=null ){
			$('input[name="call_h"]').val(record['call_time'].split(" ")[1].split(":")[0]);
			$('input[name="call_m"]').val(record['call_time'].split(" ")[1].split(":")[1]);
		}
		if( record['dep_to_site']!=null ){
			var d = new Date(record['dep_to_site']);
			$('#single_cal3').data('daterangepicker').setStartDate(moment(d).format('MM/DD/YYYY'));
			$('#single_cal3').data('daterangepicker').setEndDate(moment(d).format('MM/DD/YYYY'));
			$('input[name="dep_s_h"]').val(record['dep_to_site'].split(" ")[1].split(":")[0]);
			$('input[name="dep_s_m"]').val(record['dep_to_site'].split(" ")[1].split(":")[1]);
		}else{
			$('input[name="dep_s_h"]').val($('input[name="call_h"]').val());
			$('input[name="dep_s_m"]').val($('input[name="call_h"]').val());
		}
		if( record['arrival_on_site']!=null ){
			$('input[name="arr_s_h"]').val(record['arrival_on_site'].split(" ")[1].split(":")[0]);
			$('input[name="arr_s_m"]').val(record['arrival_on_site'].split(" ")[1].split(":")[1]);
		}else{
			$('input[name="arr_s_h"]').val($('input[name="dep_s_h"]').val());
			$('input[name="arr_s_m"]').val($('input[name="dep_s_m"]').val());
		}
		if( record['dep_to_dest']!=null ){
			$('input[name="dep_f_s_h"]').val(record['dep_to_dest'].split(" ")[1].split(":")[0]);
			$('input[name="dep_f_s_m"]').val(record['dep_to_dest'].split(" ")[1].split(":")[1]);
		}else{
			$('input[name="dep_f_s_h"]').val($('input[name="arr_s_h"]').val());
			$('input[name="dep_f_s_m"]').val($('input[name="arr_s_m"]').val());
		}
		if( record['arrival_to_dest']!=null ){
			$('input[name="arr_d_h"]').val(record['arrival_to_dest'].split(" ")[1].split(":")[0]);
			$('input[name="arr_d_m"]').val(record['arrival_to_dest'].split(" ")[1].split(":")[1]);
		}else{
			$('input[name="arr_d_h"]').val($('input[name="dep_f_s_h"]').val());
			$('input[name="arr_d_m"]').val($('input[name="dep_f_s_m"]').val());
		}
		if( record['unit_ready']!=null ){
			$('input[name="unit_available_h"]').val(record['unit_ready'].split(" ")[1].split(":")[0]);
			$('input[name="unit_available_m"]').val(record['unit_ready'].split(" ")[1].split(":")[1]);
		}else{
			$('input[name="unit_available_h"]').val($('input[name="arr_d_h"]').val());
			$('input[name="unit_available_m"]').val($('input[name="arr_d_m"]').val());
		}
		if( record['return_to_station']!=null ){
			$('input[name="ret_s_h"]').val(record['return_to_station'].split(" ")[1].split(":")[0]);
			$('input[name="ret_s_m"]').val(record['return_to_station'].split(" ")[1].split(":")[1]);
		}else{
			$('input[name="ret_s_h"]').val($('input[name="unit_available_h"]').val());
			$('input[name="ret_s_m"]').val($('input[name="unit_available_m"]').val());
		}
		
		$('select[name="from"]').val(record["from_type"]).trigger('change');
		$('select[name="to"]').val(record["to_type"]).trigger('change');
	
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
		
		var patients = record["patients"];
		if(patients!=null && isset(patients[0])){
			var pat = patients[0];
			var name = pat["name"];
			
			if( patients[0]["name"].split(" ").length > 1 ){
				var fname = name.split(" ")[0];
				var lname = name.replace(fname,"").trim();
				$('#first_name').val(fname);
				$('#last_name').val(lname);
			} else {
				$('#first_name').val(name);
			}
			$('#year').val(pat["age"]);
		}
	}
	
	function getUrlParameter(sParam){
		var sPageUrl = window.location.search.substring(1);
		var sURLVaraibles = sPageUrl.split('&');
		var sParameterName;
		var i;
		
		for( i=0 ; i < sURLVaraibles.length ; i++ ) {
			sParameterName = sURLVaraibles[i].split('=');
			if(sParameterName[0]===sParam){
				return sParameterName[1]===undefined?undefined:decodeURIComponent(sParameterName[1]);
			}
		}
		return undefined;		
	}

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
        json_data.parent_id = $("#parent_id").val();
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

		//Stupid me for thinking that
		$('.hour').each( function() {
		
			if( $ ( this ).val() < 0 || $ ( this ).val() > 23 ) {

				flag = false;
				var h_id = $ ( this ).attr( 'name' ).replace('_h','_error');
				$('#' + h_id ).text('Hour must be between 0 and 23'); 
			} 
		});

		$('.minute').each( function() {
		
			if( $ ( this ).val() < 0 || $ ( this ).val() > 59 ) {

				flag = false;
				var m_id = $ ( this ).attr( 'name' ).replace('_m','_error');
				$('#' + m_id ).text('Minute must be between 0 and 59'); 
			} 
		});

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
              //  }
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
                url:'add_emergency',
                dataType:'json',
                type: 'post',
                data: formData,
                success:function(response){
                    if(response.Status == 1){
                        alert("Emergency Added Succesfully");
                        window.location.href="report";
                    }else{
                        alert("Something went wrong, Consult IT department");
                    }
                }
            });
        }

    }

    function reset(){

        $('.team_info_form').trigger('reset');

        var pc = $('.team_info_form').length;

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

	if( name != '')
	        $('.' + name).val($val);
    }

</script>
@endsection
