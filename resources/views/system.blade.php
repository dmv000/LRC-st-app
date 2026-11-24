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
			<h3>System Functionalities</h3>
		</div>
	</div>
	<hr width=100%>
	<div class="row">
		<div class="col-md-12 col-sm-12 col-xs-12">
			<div class="x_panel">
				<div class="x_title">
					<h2>Supervisord</h2>
					<h5>&nbsp;(Send Mails)</h5>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
					</ul>
					<div class="clearfix">
					</div>
				</div>
				<div class="x_content">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<button class="btn btn-primary" type="button" onClick="checkSupervisord('check')">Check</button>
								<button class="btn btn-info" type="button" onClick="checkSupervisord('restart')">Restart</button>
							</div>
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
					<h2>Resst Users</h2>
					<ul class="nav navbar-right panel_toolbox">
						<li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
					</ul>
					<div class="clearfix">
					</div>
				</div>
				<div class="x_content">
					<fieldset>
						<div class="form-group">
							<div class="col-md-12 col-sm-12 col-xs-12">
								<button class="btn btn-primary" type="button" onClick="resetUsers()">Check</button>
							</div>
						</div>
					</fieldset>
					<div class="clearfix"></div>
				</div>   
			</div>
		</div>
	</div>
</div>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Acion Result</h4>
      </div>
      <div class="modal-body" id="modal_bd">
        <p>Please wait, this might take a couple seconds.<img id="loading-image" src="images/spinner.gif" alt="Loading..." /></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" id="close_btn">Close</button>
      </div>
    </div>

  </div>
</div>

<script>

function checkSupervisord(action){
	
	$('#myModal').modal('show');
	
	var formData = {
		"_token": "{{ csrf_token() }}",
		"action" : action,
		"type" : "supervisord"
	}
		
	$.ajax({
		url:'system_funcitonalities',
		dataType:'json',
		type: 'post',
		data: formData,
		success:function(response){
			$('#modal_bd').empty();
			$($.parseHTML('<p style="font:16px;color:red">' + response.Message.replace('\n','<br/>') + '<\p>')).appendTo('#modal_bd');
		}
	});
}

function resetUsers() {
	
	$('#myModal').modal('show');
	
	var formData = {
		"_token": "{{ csrf_token() }}",
		"type" : "resetUsers"
	}
		
	$.ajax({
		url:'system_funcitonalities',
		dataType:'json',
		type: 'post',
		data: formData,
		success:function(response){
			$('#modal_bd').empty();
			$($.parseHTML('<p style="font:16px;color:red">' + response.Message.replace('\n','<br/>') + '<\p>')).appendTo('#modal_bd');
		}
	});
}


$(document).ready(function() {

});
</script>
@endsection
