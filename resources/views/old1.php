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
                    <h2>Team Information</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="well" style="overflow: auto">
                        <form class="team_info_form">
                            <table id="add_emergency" >
                                <!--Row for car number
				//<tr>
				//	<td>
				//		<div class="formgroup">
				//			<div class="checkbox">
                            	//				<div class="checkbox">
                            	//					<label>
                              	//						<input type="checkbox" class="flat" checked="checked"> Checked
                            	//					</label>
                          	//				</div>
                          	//			</div>
				//		</div>
				//	</td>
				//</tr>-->
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Car number :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <select class="form-control" id="car_number">
                                                    <option value="-1"></option>
                                                    @foreach($ambulances as $ambulance)
                                                    <option value=" {{ $ambulance->id }} ">{{ $ambulance->number }}</option>
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
                                </tr>
                                <!--Row for emts-->
                                <tr style="vertical-align:top;">
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Driver :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <select class="form-control" id="driver">
                                                    <option value="-1"></option>
                                                    @foreach($drivers as $driver)
                                                    <option value="{{ $driver->id }}">{{ $driver->nickname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Mission Leader :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <select class="form-control" id="m_leader">
                                                    <option value="-1"></option>
                                                    @foreach($emts as $emt)
                                                    <option value="{{ $emt->id }}">{{ $emt->nickname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                    <td id="emts" style="max-width:350px;">
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >EMTs :&nbsp;</label>
                                            <div id="asd0" class="col-md-15 col-sm-15 col-xs-12 emts-selector" style="padding-bottom:20px;    display: -webkit-box;max-width:250px;">
                                                <select class="form-control" class="emt" onchange="add_emt(this)" >
                                                    <option value="-1"></option>
                                                    @foreach($emts as $emt)
                                                    <option value="{{ $emt->id }}">{{ $emt->nickname }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" class="btn btn-round btn-danger" style="margin-left:10px;" onclick="remove(this)">-</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span id="driver_error" class="error"></span>
                                    </td>
                                    <td class="error_td">
                                        <span id="m_leader_error" class="error"></span>
                                    </td>
                                    <td class="error_td">
                                        <span id="emt_error" class="error"></span>
                                    </td>
                                </tr>
                                <!--Row for times-->
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Date :&nbsp;</label>
                                            <div class="col-md-9 xdisplay_inputx form-group has-feedback">
                                                <input type="text" class="form-control has-feedback-left" id="single_cal3" placeholder="Date" value=" {{ $date }} " aria-describedby="inputSuccess2Status3">
                                                <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                                <span id="inputSuccess2Status2" class="sr-only">(success)</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Departure time (وقت الانطلاق) :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12" style="font-size:14px;">
                                                <input type="number" id="dep_hour" min=0 max=23 value=12 onchange="pad_me(this)"></input>
                                                <input type="number" id="dep_min" min=0 max=59 value=00 onchange="pad_me(this)"></input>
                                                <span>&nbsp;&nbsp;(00:00 -> 23:59)</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >ٌReturn time (وقت العودة) :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12" style="font-size:14px;">
                                                <input type="number" id="ret_hour" min=0 max=23 value=12 onchange="pad_me(this)"></input>
                                                <input type="number" id="ret_min" min=0 max=59 value=00 onchange="pad_me(this)"></input>
                                                <span>&nbsp;&nbsp;(00:00 -> 23:59)</span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>

            <div class="x_panel copy_me">
                <div class="x_title">
                    <h2>Patient Information</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a onclick="hide_me(this)"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="well" style="overflow: auto">
                        <form class="patient_info_form">
                            <div>
                                <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Mission Type :&nbsp;</label>
                                <div class="col-md-3 col-sm-3 col-xs-12">
                                    <select id='mission_type_1' onchange='blabla()' class="select2_group form-control mission_type" style="margin-bottom:20px;">
                                        <option value="-1"></option>
                                        @foreach($mission_subtypes as $mission_subtype)
                                        <optgroup label="{{ $mission_subtype->description }}">
                                            @foreach($mission_subtype->mission_types as $mission_type)
                                            <option value="{{ $mission_type->id }}">{{ $mission_type->name }}</option>
                                            @endforeach
                                        </optgroup>
                                        @endforeach
                                    </select>
                                    <sapn class="mission_type_error error"></span>
                                </div>
                            </div>
                            <hr width="100%">
                            <table>
				<tr>
					<td>
						<div class="checkbox">
                            				<label>
                              					<input onchange="blablabla(this)" type="checkbox" class="annuler flat"> Annulé
                            				</label>
                          			</div>
					</td>
				</tr>
                                <tr>
                                    <td>
                                        <span>First Name :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <input type="text" class="form-control has-feedback-left first_name" id="first_name" placeholder="First Name">
                                            <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span>Last Name :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <input type="text" class="form-control has-feedback-left last_name" id="last_name" placeholder="Last Name">
                                            <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span class="first_name_error error"></span>
                                    </td>
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span class="last_name_error error"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span>Year of Birth :&nbsp;</span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                                            <input type="number" min="1899" max="{{ $current_year }}" class="form-control has-feedback-left year" id="year" placeholder="Year of birth">
                                            <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span class="year_error error"></span>
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
                                            <input type="text" maxlength="100" class="form-control complaint" placeholder="Chief Complaint" style="width:350px;font-size:17px;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td class="error_td">
                                        <span class="complaint_error error"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span>
                                            Material Used :&nbsp;
                                        </span>
                                    </td>
                                    <td>
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <select class="form-control all_material" onchange="addMaterial(this)">
                                                <option value="-1"></option>
                                                @foreach($material as $mat)
                                                <option>{{ $mat->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="col-md-12 col-sm-12 col-xs-12 form-group">
                                            <textarea class="material" style="width:358px;height:60px;" disabled></textarea>
                                            <a class="btn btn-app" onclick="undo(this)">
                                                <i class="fa fa-undo"></i>
                                                Undo
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="AddMaterialError error">
                                        </span>
                                    </td>
                                </tr>
                            </table>
                            <hr width="100%">
                            <table>
                                <tr>
                                    <td>
                                        <div class="form-group" >
                                            <label class="control-label col-md-3 col-sm-3 col-xs-3">From &nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-9">
                                                <select class="form-control from" onchange="change_from(this)">
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
                                            <select class="form-control from_specific_0" style="width:240px;display:none">
                                                <option value="-1"></option>
                                                @foreach($hospitals as $hospital)
                                                <option value="{{ $hospital->id }}">{{ $hospital->code . " (" . $hospital->name . ")" }}</option>
                                                @endforeach
                                            </select>
                                            <select class="form-control from_specific_1" style="width:240px;display:none">
                                                <option value="-1"></option>
                                                @foreach($locations as $location)
                                                <option value = " {{ $location->id }} ">{{ $location->village . " (" . $location->station . ")" }}</option>
                                                @endforeach
                                            </select>
                                            <select class="form-control from_specific_3" style="width:240px;display:none">
                                                <option value="-1"></option>
                                                @foreach($stations as $station)
                                                <option value = " {{ $station->id }} ">{{ $station->id . " (" . $station->name . ")"  }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" maxlength="100" class="form-control from_specific_2" placeholder="From ... " style="width:240px;display:none">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span class="from_error error"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group" >
                                            <label class="control-label col-md-3 col-sm-3 col-xs-3">To   &nbsp;&nbsp;&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-9">
                                                <select class="form-control to" onchange="change_to(this)">
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
                                            <select class="form-control to_specific_0" style="width:240px;display:none">
                                                <option value="-1"></option>
                                                @foreach($hospitals as $hospital)
                                                <option value="{{ $hospital->id }}">{{ $hospital->code . " (" . $hospital->name . ")" }}</option>
                                                @endforeach
                                            </select>
                                            <select class="form-control to_specific_1" style="width:240px;display:none">
                                                <option value="-1"></option>
                                                @foreach($locations as $location)
                                                <option value = " {{ $location->id }} ">{{ $location->village . " (" . $location->station . ")" }}</option>
                                                @endforeach
                                            </select>
                                            <select class="form-control to_specific_3" style="width:240px;display:none">
                                                <option value="-1"></option>
                                                @foreach($stations as $station)
                                                <option value = " {{ $station->id }} ">{{ $station->id . " (" . $station->name . ")"  }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" maxlength="100" class="form-control to_specific_2" placeholder="To ... " style="width:240px;display:none">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span class="to_error error"></span>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
            </div>
            <div class="x_panel" id="bottom_panel">
                <div class="x_title">
                    <h2>Actions</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="well" style="overflow: auto">
                        <button type="button" class="btn btn-success btn-lg" onclick="reset()">Reset</button>
                        <button type="button" class="btn btn-success btn-lg" onclick="submit()">Submit</button>
                        <button type="button" class="btn btn-success btn-lg" onclick="another()">Add another patient</button>
                        <button type="button" class="btn btn-success btn-lg" onclick="remover()" id="rm_pt" style="display:none;">Remove last patient</button>
                    </div>
                </div>
            </div>
        </div>
        <br />
    </div>
</div>


<script>

	function blabla(){
		var selectedId = $('#mission_type_1').val();
		if(selectedId == '13'){
			$("#car_number").prop('disabled', true);
			$("#driver").prop('disabled', true);
		}else{
			$("#car_number").prop('disabled', false);
			$("#driver").prop('disabled', false);
		}

	}

	function blablabla(e){

		var selectedId = $(e).is(':checked');
		if(selectedId){
			$(e).parent().parent().parent().parent().parent().find('.first_name').first().prop('disabled', true);
			$(e).parent().parent().parent().parent().parent().find(".year").prop('disabled', true);
			$(e).parent().parent().parent().parent().parent().find(".last_name").prop('disabled', true);
		}else{
			$(e).parent().parent().parent().parent().parent().find(".first_name").prop('disabled', false);
			$(e).parent().parent().parent().parent().parent().find(".year").prop('disabled', false);
			$(e).parent().parent().parent().parent().parent().find(".last_name").prop('disabled', false);
		}

	}

    var count = 0;
    var countall = 1;

	$(document).ready(function(){

	});

    function submit(){
        $('.error').text('');
        var flag = true;



        $car_number = $('#car_number').val();
        if($car_number == "-1" && $('#mission_type_1').val() != "13"){
            $('#car_number_error').text('You must select an ambulance');
            flag = false;
        }else{
            $('#car_number_error').text("");
        }

        $driver_id = $('#driver').val();
        if($driver_id == "-1"  && $('#mission_type_1').val() != "13"){
            $('#driver_error').text('You must select a driver');
            flag = false;
        }else{
            $('#driver_error').text("");
        }

        $m_leader_id = $('#m_leader').val();
        if($m_leader_id == "-1"){
            $('#m_leader_error').text('You must select a mission leader');
            flag = false;
        }else{
            $('#m_leader_error').text("");
        }

        $emts = [];
        $('.emts-selector').each(function(index, element){
            if($.inArray($(element).find('select').first().val(), $emts) >= 0){
                $('#emt_error').text($(element).find('select :selected').first().text() + ' selected twice');
                flag = false;
                return false;
            }else{
                $('#emt_error').text("");
                if($(element).find('select').first().val() !="-1")
                    $emts.push($(element).find('select').first().val());
            }
        });

        $start_date = $('#single_cal3').val();
        $dep_hour = $('#dep_hour').val();
        $dep_min = $('#dep_min').val();
        $ret_hour = $('#ret_hour').val();
        $ret_min = $('#ret_min').val();

        //Patient Info
        var patientInfo = [];

        var pts = $('.copy_me');

        pts.each(function(index, element){
            patientInfo[index] = {};
            var flag2 = true;

            $mission_type = $(element).find('.mission_type').first().val();
            if($mission_type == "-1"){
                $(element).find('.mission_type_error').first().text('You must select a mission type');
                flag = false;
                flag2=false;
            }else{
                $(element).find('.mission_type_error').first().text('');
            }

	    $skipcheck = $(element).find('.annuler').first().is(':checked');

            $first_name = $(element).find('.first_name').first().val();
            if($first_name.trim() == "" && !$skipcheck){
                $(element).find('.first_name_error').first().text('You must add a first name');
                flag = false;
                flag2=false;
            }else{
                $(element).find('.first_name_error').first().text('');
            }

            $last_name = $(element).find('.last_name').first().val();
            if($last_name.trim() == ""  && !$skipcheck){
                $(element).find('.last_name_error').first().text('You must add a last name');
                flag = false;
                flag2=false;
            }else{
                $(element).find('.last_name_error').first().text('');
            }

            $year = $(element).find('.year').first().val();
            if($year.trim() == ""  && !$skipcheck){
                $(element).find('.year_error').first().text('You must select a year of birth');
                flag = false;
                flag2=false;
            }else{
                $(element).find('.year_error').first().text('');
            }

            $complaint = $(element).find('.complaint').first().val();
            if($complaint.trim() == ""){
                $(element).find('.complaint_error').first().text('You must add a chief complaint');
                flag = false;
                flag2=false;
            }else{
                $(element).find('.complaint_error').first().text('');
            }

            $material = $(element).find('.material').first().val().trim();

            $from = $(element).find('.from').first().val();
            $from_specific = "";
            if($from == "-1"){
                $(element).find('.from_error').first().text('You must choose a location');
                flag = false;
                flag2 = false;
            }else{
                switch ($from) {
                    case "0":
                        $from_specific = $(element).find('.from_specific_0').first().val().trim();
                        break;
                    case "1":
                    case "2":
                        $from_specific = $(element).find('.from_specific_1').first().val().trim();
                        break;
                    case "4":
                        $from_specific = $(element).find('.from_specific_2').first().val().trim();
                        break;
                    case "3":
                        $from_specific = $(element).find('.from_specific_3').first().val().trim();
                        break;
                    default:
                        $from_specific = "";
                        break;
                }
                if($from_specific == "" || $from_specific == "-1"){
                    $(element).find('.from_error').first().text('You must choose a location');
                    flag = false;
                    flag2 = false;
                }
            }

            $to = $(element).find('.to').first().val();
            $to_specific = "";
            if($to == "-1"){
                $(element).find('.to_error').first().text('You must choose a location');
                flag = false;
                flag2 = false;
            }else{
                switch ($to) {
                    case "0":
                        $to_specific = $(element).find('.to_specific_0').first().val().trim();
                        break;
                    case "1":
                    case "2":
                        $to_specific = $(element).find('.to_specific_1').first().val().trim();
                        break;
                    case "4":
                        $to_specific = $(element).find('.to_specific_2').first().val().trim();
                        break;
                    case "3":
                        $to_specific = $(element).find('.to_specific_3').first().val().trim();
                        break;
                    default:
                        $to_specific = "";
                        break;
                }
                if($to_specific == "" || $to_specific == "-1"){
                    $(element).find('.to_error').first().text('You must choose a location');
                    flag = false;
                    flag2 = false;
                }
            }

            if(flag2){
                $(element).find('.x_title h2').removeClass('error_h2');
            }else{
                $(element).find('.x_title h2').addClass('error_h2');
            }

            patientInfo[index]['mission_type'] = $mission_type;
            patientInfo[index]['first_name'] = $first_name;
            patientInfo[index]['last_name'] = $last_name;
            patientInfo[index]['year'] = $year;
            patientInfo[index]['complaint'] = $complaint;
            patientInfo[index]['material'] = $material;
            patientInfo[index]['from'] = $from;
            patientInfo[index]['from_specific'] = $from_specific;
            patientInfo[index]['to'] = $to;
            patientInfo[index]['to_specific'] = $to_specific;
	    patientInfo[index]['annuler'] = $skipcheck;


        });

        if(flag){
            var obj = {};
            obj['car_number'] = $car_number;
            obj['driver'] = $driver_id;
            obj['m_leader'] = $m_leader_id;
            obj['emt'] = $emts;
            obj['mission_date'] = $start_date;
            obj['dep_hour'] = $dep_hour;
            obj['dep_min'] = $dep_min;
            obj['ret_hour'] = $ret_hour;
            obj['ret_min'] = $ret_min;
            obj['patients'] = patientInfo;

            var formData = {
                form : JSON.stringify(obj)
            }

            $.ajax({
                url:'add_emergency',
                dataType:'json',
                type: 'post',
                data: formData,
                success:function(response){
                    if(response.Status == 1){
                        alert("Emergency Added Succesfully");
                        window.location.href="main";
                    }
                }
            });

        }

    }

    function reset(){
        $('.patient_info_form').trigger('reset');
        $('.team_info_form').trigger('reset');

        for (var i = 1; i <= count; i++) {
            $elem = $('.team_info_form').find('#asd' + i);
            $elem.remove();
        }
        count = 0;
        countall = 0;
    }

    function hide_me(e){
        $par = $(e).parent().parent().parent().parent();
        $con = $par.find('.x_content').first();
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

    function another(){
        $new = $('.copy_me').first().clone();
        $new.find('.patient_info_form').trigger('reset');
        $new.insertBefore('#bottom_panel');
        $("#rm_pt").show();
    }

    function remover(){
        $ps = $('.copy_me');
        if($ps.length == 2){
            $ps.last().remove();
            $("#rm_pt").hide();
        }else if($ps.length > 2){
            $ps.last().remove();
        }
    }

    function change_from(e){
        $par = $(e).parent().parent().parent().parent();

        $choice = $(e).val();


        switch ($choice) {
            case "-1":
                $par.find('.from_specific_0').hide();
                $par.find('.from_specific_1').hide();
                $par.find('.from_specific_2').hide();
                $par.find('.from_specific_3').hide();
                break;
            case "0":
                $par.find('.from_specific_0').show();
                $par.find('.from_specific_1').hide();
                $par.find('.from_specific_2').hide();
                $par.find('.from_specific_3').hide();
                break;
            case "1":
            case "2":
                $par.find('.from_specific_0').hide();
                $par.find('.from_specific_1').show();
                $par.find('.from_specific_2').hide();
                $par.find('.from_specific_3').hide();
                break;
            case "3":
                $par.find('.from_specific_0').hide();
                $par.find('.from_specific_1').hide();
                $par.find('.from_specific_2').hide();
                $par.find('.from_specific_3').show();
                break;
            case "4":
                $par.find('.from_specific_0').hide();
                $par.find('.from_specific_1').hide();
                $par.find('.from_specific_2').show();
                $par.find('.from_specific_3').hide();
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
                $par.find('.to_specific_0').hide();
                $par.find('.to_specific_1').hide();
                $par.find('.to_specific_2').hide();
                $par.find('.to_specific_3').hide();
                break;
            case "0":
                $par.find('.to_specific_0').show();
                $par.find('.to_specific_1').hide();
                $par.find('.to_specific_3').hide();
                $par.find('.to_specific_2').hide();
                break;
            case "1":
            case "2":
                $par.find('.to_specific_0').hide();
                $par.find('.to_specific_1').show();
                $par.find('.to_specific_3').hide();
                $par.find('.to_specific_2').hide();
                break;
            case "3":
                $par.find('.to_specific_0').hide();
                $par.find('.to_specific_1').hide();
                $par.find('.to_specific_3').show();
                $par.find('.to_specific_2').hide();
                break;
            case "4":
                $par.find('.to_specific_0').hide();
                $par.find('.to_specific_1').hide();
                $par.find('.to_specific_3').hide();
                $par.find('.to_specific_2').show();
                break;
            default:
                break;

        }

    }

    function undo(e){
        ClearAddMaterialError(e);
        $par = $(e).parent().parent().parent().parent();
        $vals = $par.find('.material').first().val().split(";");
        $vals.pop();
        $vals.pop();
        if($vals.length > 0)
            $par.find('.material').first().val($vals.join(';') + "; ");
        else{
            $par.find('.material').first().val("");
        }
    }

    function addMaterial(e){
        ClearAddMaterialError(e);
        $par = $(e).parent().parent().parent().parent();
        $val = $(e).val();
        if($val == "-1") return;
        if($par.find('.material').first().val().indexOf($val) < 0){
            $par.find('.material').first().val($par.find('.material').first().val() + $val + "; ");
        }else{
            $par.find('.AddMaterialError').first().text($val + " already Exist(s)");
        }
    }

    function ClearAddMaterialError(e){
        $par = $(e).parent().parent().parent().parent();
        $par.find('.AddMaterialError').first().text("");
    }

    function pad_me(e){
        $val =  $(e).val();
        if($val.length == 1){
            $val = "0" + $val;
            $(e).val($val);
        }
    }

    function remove(e){
        if(countall > 1){
            $(e).parent().remove();
            countall--;
        }
    }

    function add_emt(e){
        selected = $(e).val();
        if(selected != "-1"){
            if($('#asd' + count).find('select').first().val() != -1){
                count++;
                countall++;
                $('.emts-selector').first().clone().attr({id : "asd" + count, name: "asd"}).appendTo('#emts');
            }
        }else{
            if(countall>1){
                $(e).parent().remove();
                countall--;
            }
        }
    }
</script>
@endsection
