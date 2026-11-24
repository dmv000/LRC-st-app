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
            <h3>Reset User Password</h3>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Information</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                        <form id="info_form">
                            <table id="add_village" >
				<tr>
      				    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >User&nbsp;</label>
                                            <div>
                                                <select class="form-control standardSelect" id="user" name="user" style="display:none;" tabindex="-1" data-placeholder="Choose a User to Reset Password...">
                                                    <option value="-1"></option>
                                                    @foreach(\App\User::where('is_deleted','0')->get() as $user)
                                                    <option value="{{ $user->nickname }}">{{ $user->name }} ({{ $user->nickname }}) </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
				    <td class="error_td">
                                        <span id="user_error" class="error"></span>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Your Password ( {{ Auth::user()->nickname }} )&nbsp;</label>
                                            <div>
                                                <input id="pass" name="pass" style="width:100%;padding:4px;font-size:15px;" type="password"/>
                                            </div>
                                        </div>
                                    </td>
				</tr>
				<tr>
                                    <td class="error_td">
                                        <span id="password_error" class="error"></span>
                                    </td>
                                </tr>
                                <!--Row for emts-->
            								
                            </table>

                        </form>
                    <div>
                        <button type="button" class="btn btn-success btn-lg" onclick="submit()">Reset Password</button>
                    </div>

                </div>
            </div>

                    </div>
        </div>
        <br />
    </div>
</div>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Resetting Password</h4>
      </div>
      <div class="modal-body" id="modal_bd">
        <p>Please wait, this might take a couple seconds.<img id="loading-image" src="images/spinner.gif" alt="Loading..." /></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" disabled id="close_btn" onclick="window.location.href='/reset_pass'">Close</button>
      </div>
    </div>

  </div>
</div>

<script>

    $(document).ready(function(){
        $(".standardSelect").chosen({
            disable_search_threshold: 3,
            no_results_text: "Oops, nothing found!",
            width: "100%",
            disabled: true
        });
    });

function submit() {

    $('.error').text('');
    var flag = true;
    flag = verify();
    if(!flag) return;

	var data = {};
	data.vuser = $('#user').val();
	data.yuser = "{{ Auth::user()->nickname }}";
	data.ypass = $('#pass').val();

$('#myModal').modal('show');

    //Serialize the form
    var formData = {
        "_token": "{{ csrf_token() }}",
        "form" : data
    }
            
    $.ajax({
        url:'/reset_pass',
        dataType:'json',
        type: 'post',
        data: formData,
        success:function(response){
            if(response.Status == 1){
                //alert(response.Message);
		$('#modal_bd').empty();
		$($.parseHTML(response.Message)).appendTo('#modal_bd');
		$('#close_btn').prop('disabled', false);
            }else{
                $('#modal_bd').empty();
		$($.parseHTML('<p>' + response.Message + '<\p>')).appendTo('#modal_bd');
		$('#close_btn').prop('disabled', false);
            }
        }
    });
}

function verify() {

    var flag = true;
    //User selected
    var user = $('#user').val();
    if(user == -1){

        $('#user_error').text('Set the User Name');
        flag = false;
    }

    //Password
    var pass = $('#pass').val();
    if(pass == null || pass == ""){

        $('#password_error').text('Please set your password');
        flag = false;
    }  

    return flag;
}

</script>
@endsection
