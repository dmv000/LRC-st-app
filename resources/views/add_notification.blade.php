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
            <h3>Page</h3>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Information/معلومات</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="well" >
                        <form id="info_form">
                            <table id="add_notification" >
                                <!--Row for emts-->
                                <tr style="vertical-align:top;">					
				    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Group : </label>
					    <table style="min-width:300px;">
						<tr id="user_row">
					            <td style="padding: 5px; widt: 300px;">
						<select multiple class="form-control standardSelect" name="user_id" id="user_id" style="display:none;" tabindex="-1" data-placeholder="Choose a User...">
                                                    <option value="-1"></option>
                                                    @foreach(\App\User::where('is_deleted',0)->get() as $user)
                                                    <option value=" {{ $user->id }} ">{{ $user->name . " (" . $user->nickname . ")"}}</option>
                                                    @endforeach
                                                </select>
                                            	    </td>
						</tr>
						<tr id="group_row">
					            <td style="padding: 5px; width:300px;">
						<select class="form-control standardSelect" name="group" id="group" style="display:none;" tabindex="-1" data-placeholder="Choose a Group..." >
                                                    <option value="-1"></option>
                                                    @foreach(\App\Http\ORM\Group::get() as $station)
                                                    <option value=" {{ $station->id }} ">{{ $station->description }}</option>
                                                    @endforeach
                                                </select>
                                            	    </td>
						</tr>
						<tr id="all_users_row">					
						    <td>
						<input type="checkbox" id="all" onchange="blabla(this)">&nbsp;All Users</input>
                                            	    </td>
						</tr>
						<tr id="one_user_row">					
						    <td>
						<input type="checkbox" id="user" onchange="bla(this)"/>&nbsp;Multiple Users</input>
                                            	    </td>
						</tr>
					    </table>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span id="group_error" class="error"></span>
                                    </td>
                                </tr>
                                <tr style="min-width:500px;">
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-left:0px;" >Notification Text : &nbsp;</label>
                                            <div class="form-group">
                                                <input id="text" name="text" style="width:100%;padding:4px;font-size:15px;" type="text"/>
                                            </div>
                                        </div>
                                    </td>
</tr>
                                <tr>
                                    <td class="error_td">
                                        <span id="text_error" class="error"></span>
                                    </td>
                                </tr>
<tr style="min-width:500px;">
				    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-left:0px;" >Link : &nbsp;</label>
                                            <div class="form-group">
                                                <input id="link" name="text" style="width:100%;padding:4px;font-size:15px;" type="text"/>
                                            </div>
                                        </div>
                                    </td>									
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span id="link_error" class="error"></span>
                                    </td>
                                </tr>
                                <!--Row for times-->
                                		
                            </table>
                        </form>
                    </div>
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
                        </div>
                </div>
            </div>
        </div>
        <br />
    </div>
</div>


<script>
function bla(e) {

if( $(e).is(':checked') ) {
	$("#user_row").show();
	$("#group_row").hide();
	$("#all_users_row").hide();
}else{
	$("#user_row").hide();
	$("#group_row").show();
	$("#all_users_row").show();
}
}

function blabla(e) {

$('#all_users_row').show();

if( $(e).is(':checked') ) {
	$('#user_row').hide();
	$('#group_row').hide();
	$('#one_user_row').hide();
}else {
	$('#user_row').hide();
	$('#group_row').show();
	$('#one_user_row').show();
}
}

function reset() {
	$('.error').text('');
	$('#info_form')[0].reset();	
	$('.standardSelect').val(-1).trigger('chosen:updated');
	$('#user_row').hide();
	$('#group_row').show();
	$('#one_user_row').show();
	$("#all_users_row").show();
	$(':checkbox').prop('checked',false);
}

    $(document).ready(function(){
        $(".standardSelect").chosen({
            disable_search_threshold: 5,
            no_results_text: "Oops, nothing found!",
            width: "100%",
            disabled: true
        });

	$("#user_row").hide();
    });



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



function submit() {

    $('.error').text('');
    var flag = true;
    flag = verify();
    if(!flag) return;
    
    var data = $('#info_form').serlizeFromJSON();

    var formData = {
        "_token": "{{ csrf_token() }}",
        "form" : JSON.stringify(data)
    }
            
    $.ajax({
        url:'notification',
        dataType:'json',
        type: 'post',
        data: formData,
        success:function(response){
            if(response.Status == 1){
                alert("Notification Added Succesfully");
                window.location.href="/";
            }else{
                alert(response.Message);
            }
        }
    });
}

function verify() {

    var flag = true;
    
//Who to notify
    var one_user = $('#user').is(':checked');
    var all_users = $('#all').is(':checked');

if(!one_user && !all_users){
	//A group should be selected
	if( $('#group').val() == null || $('#group').val() == "-1" ) {
		flag = false;  
		$('#group_error').text('Select a group');
	}
}
else if(one_user == true) {
	if( $('#user_id').val() == null || $('#user_id').val() == "-1" ) {
		flag = false;  
		$('#group_error').text('Select a User');
	}
}

    var text = $('#text').val();
    if( text == ""){

        $('#text_error').text('Please add Notification Text');
        flag = false;
    }

    var link = $('#link').val();
    if( link == ""){

        $('#link_error').text('Please add Notification Link');
        flag = false;
    }
    return flag;
}

</script>
@endsection
