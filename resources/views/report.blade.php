@extends('layouts.master')
@section('content')
<style>
	table {
		white-space: nowrap;
	}
	
	div.checkable {
		cursor : pointer
	}
</style>
<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3></h3>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Date Range</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <fieldset>
                        <div class="control-group">
                            <div class="controls">
                                <div class="col-md-3 xdisplay_inputx form-group has-feedback" style="margin-left:40%">
                                    <input type="text" class="form-control has-feedback-left" id="single_cal3" placeholder="Current Date" aria-describedby="inputSuccess2Status3">
                                    <input type="text" style="margin-top:10px;padding-left:40%;" disabled class="form-control has-feedback-left" id="dayName">
                                    <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                    <span id="inputSuccess2Status3" class="sr-only">(success)</span>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                <div class="x_title">
                    <h2>All Emergencies for the day</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="overflow:scroll">
                    <table id="example" class="display" width="100%"></table>
                </div>
                <div class="x_title">
                    <h2>Comment Section</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <textarea style="min-width:100%;margin-bottom:10px;font-size:xx-large;direction:rtl;overflow:hidden;" disabled id="oldComment"></textarea>
                    <!--<textarea style="min-width:100%;height:100px; margin-bottom:10px;font-size:xx-large;direction:rtl" oninput="showSave()" id="newComment"></textarea>-->
                    <button type="button" id="saveButton" style="margin-top:15px;display:none;" onclick="saveChanges()" class="btn btn-info btn-lg">Save Changes</button>
                </div>
                <div class="x_title">
                    <h2 id="rapport_name">Rapport</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <!--<textarea style="min-width:100%; margin-bottom:10px;font-size:xx-large;direction:rtl;overflow:hidden"  disabled id="rapport"></textarea>-->
                    <table style="font-size:24px" class="table table-hover">
                      <thead>
                        <tr>
                          <th>Type / النوع</th>
                          <th>Number / العدد</th>
                          @if($casualties)<th>Casualties / الاصابات</th>@endif
                        </tr>
                      </thead>
                      <tbody>
                        @for($i=0;$i < count($report);$i++)
                            <tr>
                                <td>{{ $report[$i]['name'] }}</td>
                                <td>{{ $report[$i]['count'] }}</td>
                                @if(count($report[$i]) == 3)<td>{{ $report[$i]['casualties'] }}</td>@else @if($casualties)<td>- -</td>@endif @endif
                            </tr>
                        @endfor
                      </tbody>
                    </table>

                    <div class="form-group">
                        <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >From :&nbsp;</label>
                        <div class="col-md-4 col-sm-4 col-xs-4" id="from_select">
                            <select class="form-control standardSelect" id="report_from" tabindex="-1" data-placeholder="Choose an EMT..." style="display:none;">
                                <option value="-1"></option>
                                @foreach($emts as $emt)
                                <option value="{{ $emt->id }}">{{ $emt->nickname }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-4 form-group has-feedback" id="from_selected">
                            <input type="text" class="form-control has-feedback-left" disabled id="reported_from">
                            <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                        </div>
                        <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >To :&nbsp;</label>
                        <div class="col-md-4 col-sm-4 col-xs-4 form-group has-feedback">
                            <input type="text" class="form-control has-feedback-left" id="inputSuccess2" placeholder="Name">
                            <span class="fa fa-user form-control-feedback left" aria-hidden="true"></span>
                            <button type="button" id="saveButton2" style="margin-top:15px;display:none;" onclick="savefromTo()" class="btn btn-info btn-lg">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="../build/js/jquery.dataTables.min.js"></script>
<!-- YAMLI CODE START -->
<script type="text/javascript" src="https://api.yamli.com/js/yamli_api.js"></script>
<script type="text/javascript">
  if (typeof(Yamli) == "object" && Yamli.init( { uiLanguage: "en" , startMode: "onOrUserDefault" } ))
  {
    Yamli.yamlify( "newComment", { settingsPlacement: "inside" } );
  }
</script>
<!-- YAMLI CODE END -->
<script>

    $(function () {
    $('[data-toggle="tooltip"]').tooltip()
    })

var comm = "{{ $comm }}";
var date = "{{ $date }}";
var count = {{ $count }};
var from_selected = "{{ $from}}";
var to_selected = "{{ $to}}";
var Days = ['Sunday - الأحد','Monday - الاثنين','Tuesday - الثلاثاء','Wednesday - الأربعاء','Thursday - الخميس','Friday - الجمعة','Saturday - السبت'];
var dataSet = [];
var new_comm ="";

function reloadWithNewDate(){
    var selectedDate = $('#single_cal3').val();
    var arr = selectedDate.split("/");
    var temp = arr[2];
    arr[2] = arr[1];
    arr[1] = arr[0];
    arr[0] = temp;
    selectedDate = arr.join("-");
    if(selectedDate != date){
        window.location = "/report?Date=" + selectedDate;
    }
}


function showSave(){

    $("#oldComment").val(comm + "\n" + $('#newComment').val());
    new_comm = $('#newComment').val();
    if($('#newComment').val() == ""){
        $('#saveButton').hide();
    }else{
        $('#saveButton').show();
    }
}

function savefromTo(){
    var from = $('#report_from').val();
    var to = $('#inputSuccess2').val();


    if(from != "-1" && to != ""){
        var formData = {
            from :from,
            to: to,
            date : date,
            "_token": "{{ csrf_token() }}"                        
        }

        $.ajax({
            url:'update_to_from',
            dataType:'json',
            type: 'post',
            data: formData,
            success:function(response){
                if(response.Status == 1){
                    alert("Updated Succesfully");
                    $('#from_select').hide();
                    $('#from_selected').show();
                    $('#reported_from').val(response.from);
                    $('#inputSuccess2').prop("disabled", true);
                    $('#inputSuccess2').val(to);
                    $('#saveButton2').hide();
                }
            }
        });
    }else{
        alert("Fill requested Fields");
    }
}

function delete_mission(id){
        var formData = {
            id :id,
            "_token": "{{ csrf_token() }}"                        
        }

        $.ajax({
            url:'delete_emergency',
            dataType:'json',
            type: 'post',
            data: formData,
            success:function(response){
                if(response.Status == 1){
                    alert("Urgence Removed Succesfully");
                    location.reload();
                }else {
		    alert(response.Message);
		}
            }
        });
}

function check_mission(id, div) {

	var formData = {
		id :id,
		"_token": "{{ csrf_token() }}"                        
	}

	$.ajax({
		url:'check_emergency',
		dataType:'json',
		type: 'post',
		data: formData,
		success:function(response){
			if(response.Status == 1){
				$(div).find('span').css('color','green');
				$(div).removeClass('checkable');
			}else {
				alert(response.Message);
			}
		}
	});
}

surveyInfo = {}

function displaySurveyInfo(missionId) {
    filled = surveyInfo[missionId]['filled'];
    unfilled = surveyInfo[missionId]['unfilled'];

    textFilled = "<div><h4>Surveys Filled:</h4><hr>";
    filled.forEach(function(name) {
        textFilled += "<span>" + name + "</span>";
    });
    textFilled += "</div>";
    textUnfilled = "<div><h4>Surveys Unilled:</h4><hr>";
    unfilled.forEach(function(name) {
        textUnfilled += "<span>" + name + "</span>";
    });
    textUnfilled += "</div>";


    text = "<div id='surveyInfo"+missionId+"'>" + ( filled.length > 0 ? textFilled : '' ) +( unfilled.length > 0 ? textUnfilled : '' )+ "</div>";
    console.log(text);
}

function saveChanges(){

    //$('#single_cal3').setAttribute("onChange",)
    var formData = {
        form : $('#newComment').val().trim(),
        date : date,
        "_token": "{{ csrf_token() }}"
    }

    $.ajax({
        url:'update_comment',
        dataType:'json',
        type: 'post',
        data: formData,
        success:function(response){
            if(response.Status == 1){
                alert("Comment updated Succesfully");
                comm = $('#oldComment').val();
                $('#newComment').val("");
                $('#saveButton').hide();
                setHeight($('#oldComment'));
            }
        }
    });
}



function setHeight(jq_in){
    jq_in.each(function(index, elem){
        // This line will work with pure Javascript (taken from NicB's answer):
        elem.style.height = elem.scrollHeight+'px'; 
    });
}



$(document).ready(function() {


        $(".standardSelect").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%",
            disabled: true
        });

    var dataSet = [
        @for($i=count($tableData) - 1; $i >= 0 ; $i--)
            [
	    "",
                    "{{$tableData[$i][0]}}",	   
                    "{{$tableData[$i][1]}}",
                    "{{$tableData[$i][14]}}",
                    "{{$tableData[$i][2]}}",
                    "{{$tableData[$i][3]}}",
                    "{{$tableData[$i][4]}}",
                    "{{$tableData[$i][5]}}",
                    "{{$tableData[$i][6]}}",
                    "{{$tableData[$i][7]}}",
                    "{{$tableData[$i][8]}}",
                    "{{$tableData[$i][9]}}",
                    "{{$tableData[$i][10]}}",
                    "{{$tableData[$i][11]}}",
                    "{{$tableData[$i][12]}}",
                    "{{$tableData[$i][13]}}"
            ],
        @endfor
    ]



    var table = $('#example').DataTable( {
        data: dataSet,
        columns: [
            { title: ""},
            { title: ""},
            { title: ""},
            { title: ""},
            { title: ""},
            { title: "الانطلاق"},
            { title: "العودة"},
            { title: "السيّارة"},
            //{ title: "المسعفين" },
            { title: "المسعفين"},
            { title: "المريض"},
            { title: "العمر"},
            { title: "الحالة"},
            { title: "النوع"},
            { title: "من"},
            { title: "الى"},
            { title: "O2"}
        ],

        "searching": false,
        "lengthChange": false,
        "pageLength": 15,
        "scrollX": true,
"columnDefs": [ 
            {
                "targets": 1,
                "visible": false,
                "searchable": false
            },
        {
            "targets":2,
            "data": null,
			"visible": @if( Auth::user() && Auth::user()->memberOf('Chef Equipe')) true @else false @endif,
			"render": function( data,type,row,meta){
				if(data[2] == 0){
					return "<div /*class='checkable'*/ style='display:block;text-align:center'><span style='font-size:24px' class='glyphicon glyphicon-check' aria-hidden='true'></span></div>";
				} else {
					return "<div style='display:block;text-align:center'><span style='color:green;font-size:24px' class='glyphicon glyphicon-check' aria-hidden='true'></span></div>";
				}
			}
        },
        {
            "targets":0,
            "data": null,
            "defaultContent": "<button type='button' style='margin-top: 15px;' class='btn btn-info add'>Add Patient</button>&nbsp;<button type='button' style='margin-top: 15px;' class='btn btn-info edit'>Edit</button>@if( Auth::check() && ( Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Head of Station')->first()->id) || Auth::user()->groups()->get()->contains('group_id',\App\Http\ORM\Group::where('description','Chef Equipe')->first()->id)))&nbsp;<button type='button' style='margin-top: 15px;' class='btn btn-info delete'>Delete</button>@endif"
        },
        {
            "targets":3,
            "data": null,
            "render" : function(data, type, row, meta) {
                filled = [];
                unfilled = [];
                let userSurveys = JSON.parse(data[3].replaceAll("&quot;",'"'));

                if(userSurveys.length == 0) {
                    return '';
                }

                textFilled = "\nSurveys Filled : \n";
                textUnfilled = "\nSurveys Unfilled : \n";

                userSurveys.forEach(function(userSurvey) {
                    if(userSurvey['is_filled']) {
                        filled.push(userSurvey['name']);
                        textFilled += userSurvey['name'] + "\n";
                    } else {
                        unfilled.push(userSurvey['name']);
                        textUnfilled += userSurvey['name'] + "\n";
                    }
                });

                text = "Survey Info : \n" + ( filled.length > 0 ? textFilled : '' ) + ( unfilled.length > 0 ? textUnfilled : '' );

                let color = 'red';
                if(filled.length == userSurveys.length) {
                    color = 'green';
                } else if(filled.length > 0) {
                    color = 'orange';
                }
                return '<span data-toggle="tooltip" title="' + text + '" style="color: ' + color + '; font-size: 24px;">&#x1f534;&#xfe0e;</span>';
            }
        } ]
    } );

$('#example tbody').on( 'click', 'button', function () {

	if($(this).hasClass('add')) {
        var data = table.row( $(this).parents('tr') ).data();
        window.location.href = "add_emergency?parent_id=" + data[1];
	} else if($(this).hasClass('edit')) {
		var data = table.row( $(this).parents('tr') ).data();
		window.location.href = "edit_emergency?id=" + data[1];
	} else if($(this).hasClass('delete')) {
		var data = table.row( $(this).parents('tr') ).data();
		delete_mission(data[1]);
	} 
});

$('#example tbody').on( 'click', 'div', function () {

	if($(this).hasClass('checkable')) {
		var data = table.row( $(this).parents('tr') ).data();
		check_mission(data[1],this);
	}
});

    comm = comm.replace(new RegExp('9999','g'),'\n');
    if(comm != "")
        {$('#oldComment').val(comm + "\n");}
    setHeight($('#oldComment'));
        var newDate = date.split("-");
        var temp = newDate[0];
        newDate[0] = newDate[1];
        newDate[1] = newDate[2];
        newDate[2] = temp;
        $("#single_cal3").val(newDate.join("/"));
        $('#dayName').val(Days[new Date(newDate).getDay()]);
    $('#single_cal3').change(function(e){reloadWithNewDate();});

    $('#rapport_name').text('Rapport ' + $('#single_cal3').val() + ' ' + $('#dayName').val());

    if(from_selected == ""){
        $('#from_selected').hide();
    }else{
        $('#from_select').hide();
        $('#reported_from').val("{{$from}}");
    }

    if(to_selected != ""){
        $('#inputSuccess2').val("{{$to}}");
        $('#inputSuccess2').prop("disabled", true);
    }

    if(from_selected == "" && to_selected == ""){
        $('#saveButton2').show();
    }

} );
</script>
@endsection
