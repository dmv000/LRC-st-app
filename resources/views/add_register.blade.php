<!DOCTYPE html>
<head>
  	<title>LRC {{ env('STATION_NUMBER') }} Register</title>
  	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" href="css/jquery.ui.css"/>
	<link type="text/css" rel="stylesheet" href="dd/css/footables/footable.css">

	<script src="../vendors/jquery/dist/jquery.min.js"></script>
	<script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
	<script src="js/jquery.ui.js"></script>
	<script src="dd/js/footable/footable.min.js"></script>

	<link href="css/select2.css" rel="stylesheet" />
	<script src="js/select2.js"></script>
	<script src="../vendors/moment/min/moment.min.js"></script>

<style>

.select2-dropdown{
    z-index: 10000000000;
}

.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 99; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

.option-input {
  -webkit-appearance: none;
  -moz-appearance: none;
  -ms-appearance: none;
  -o-appearance: none;
  appearance: none;
  position: relative;
  top: 13.33333px;
  right: 0;
  bottom: 0;
  left: 0;
  height: 40px;
  width: 40px;
  transition: all 0.15s ease-out 0s;
  background: #cbd1d8;
  border: none;
  color: #fff;
  cursor: pointer;
  display: inline-block;
  margin-right: 0.5rem;
  outline: none;
  position: relative;
  z-index: 1000;
}
.option-input:hover {
  background: #9faab7;
}
.option-input:checked {
  background: #40e0d0;
}
.option-input:checked::before {
  height: 40px;
  width: 40px;
  position: absolute;
  content: '✔';
  display: inline-block;
  font-size: 26.66667px;
  text-align: center;
  line-height: 40px;
}
.option-input:checked::after {
  -webkit-animation: click-wave 0.65s;
  -moz-animation: click-wave 0.65s;
  animation: click-wave 0.65s;
  background: #40e0d0;
  content: '';
  display: block;
  position: relative;
  z-index: 100;
}
.option-input.radio {
  border-radius: 50%;
}
.option-input.radio::after {
  border-radius: 50%;
}

body {
      font: 400 15px, sans-serif;
      line-height: 1.8;
      /*color: #d11717;*/
  }
  h2 {
      font-size: 24px;
      text-transform: uppercase;
      color: #303030;
      font-weight: 600;
      margin-bottom: 30px;
  }
  h4 {
      font-size: 19px;
      line-height: 1.375em;
      color: #303030;
      font-weight: 400;
      margin-bottom: 30px;
  }
  .container-fluid {
      padding: 10px 50px;
  }
  .bg-grey {
      background-color: #f6f6f6;
  }
  .logo-small {
      color: #f4511e;
      font-size: 50px;
  }
  .logo {
      color: #f4511e;
      font-size: 200px;
  }
  .thumbnail {
      padding: 0 0 15px 0;
      border: none;
      border-radius: 0;
  }
  .thumbnail img {
      width: 100%;
      height: 100%;
      margin-bottom: 10px;
  }
  .carousel-control.right, .carousel-control.left {
      background-image: none;
      color: #f4511e;
  }
  .carousel-indicators li {
      border-color: #f4511e;
  }
  .carousel-indicators li.active {
      background-color: #f4511e;
  }
  .item h4 {
      font-size: 19px;
      line-height: 1.375em;
      font-weight: 400;
      font-style: italic;
      margin: 70px 0;
  }
  .item span {
      font-style: normal;
  }
  .panel {
      border: 1px solid #f4511e; 
      border-radius:0 !important;
      transition: box-shadow 0.5s;
  }
  .panel:hover {
      box-shadow: 5px 0px 40px rgba(0,0,0, .2);
  }
  .panel-footer .btn:hover {
      border: 1px solid #f4511e;
      background-color: #fff !important;
      color: #f4511e;
  }
  .panel-heading {
      color: #fff !important;
      background-color: #f4511e !important;
      padding: 25px;
      border-bottom: 1px solid transparent;
      border-top-left-radius: 0px;
      border-top-right-radius: 0px;
      border-bottom-left-radius: 0px;
      border-bottom-right-radius: 0px;
  }
  .panel-footer {
      background-color: white !important;
  }
  .panel-footer h3 {
      font-size: 32px;
  }
/*  body.modal-open .modal {
      display: flex !important;
      height: 100%;
  } 

  body.modal-open .modal .modal-dialog {
      margin: auto;
  }*/
  .modal-dialog {
    padding-top: 100px;
  }
  .panel-footer h4 {
      color: #aaa;
      font-size: 14px;
  }
  .panel-footer .btn {
      margin: 15px 0;
      background-color: #f4511e;
      color: #fff;
  }
  .navbar {
      margin-bottom: 0;
      background-color: #d11717;
      z-index: 9999;
      border: 0;
      font-size: 12px !important;
      line-height: 1.42857143 !important;
      letter-spacing: 4px;
      border-radius: 0;
      font-family: Montserrat, sans-serif;
  }
  .navbar li a, .navbar .navbar-brand {
      color: #fff !important;
  }
  .navbar-nav li a:hover, .navbar-nav li.active a {
      color: #f4511e !important;
      background-color: #fff !important;
  }
  .navbar-default .navbar-toggle {
      border-color: transparent;
      color: #fff !important;
  }
  footer .glyphicon {
      font-size: 20px;
      margin-bottom: 20px;
      color: #f4511e;
  }
  .slideanim {visibility:hidden;}
  .slide {
      animation-name: slide;
      -webkit-animation-name: slide;
      animation-duration: 1s;
      -webkit-animation-duration: 1s;
      visibility: visible;
  }
  @keyframes slide {
    0% {
      opacity: 0;
      transform: translateY(70%);
    } 
    100% {
      opacity: 1;
      transform: translateY(0%);
    }
  }
  @-webkit-keyframes slide {
    0% {
      opacity: 0;
      -webkit-transform: translateY(70%);
    } 
    100% {
      opacity: 1;
      -webkit-transform: translateY(0%);
    }
  }
  @media screen and (max-width: 768px) {
    .col-sm-4 {
      text-align: left;
      margin: 25px 0;
    }
    .btn-lg {
        width: 100%;
        margin-bottom: 35px;
    }
  }
  @media screen and (max-width: 480px) {
    .logo {
        font-size: 150px;
    }
  }

  .page {
    display: none;
  }
  .page-active {
    display: block;
  }

  .error {
  	font-size: 12px;
    color: red;
  }

  .has-error {
  	color:red;
  	background-color: white;
  }

  .no-error {
  	color: black;
  	background-color: azure; 
  }

  .sec-error {
  	color:red;
  	background-color: white;
  }

  .no-sec-error {
  	color:black;
  	background-color: navajowhite;
  }

#myBtn {
    position: fixed; /* Fixed/sticky position */
    bottom: 20px; /* Place the button at the bottom of the page */
    right: 30px; /* Place the button 30px from the right */
    z-index: 99; /* Make sure it does not overlap */
    border: none; /* Remove borders */
    outline: none; /* Remove outline */
    background-color: red; /* Set a background color */
    color: white; /* Text color */
    cursor: pointer; /* Add a mouse pointer on hover */
    padding: 15px; /* Some padding */
    border-radius: 10px; /* Rounded corners */
    font-size: 18px; /* Increase font size */
}

  #sortable { list-style-type: none; margin: 0; padding: 0; }
  #sortable li { margin: 0 3px 3px 3px; padding: 0.4em; padding-left: 1.5em; font-size: 1.4em; }
  #sortable li span { position: absolute; margin-left: -1.3em; }

  </style>
</head>

<body id="myPage" data-spy="scroll" data-target=".navbar" data-offset="30">

<div id="services" class="container-fluid bg-grey">
<h2>Shift Register</h2>
<br>
<form id="register-form">
  <div class="page-active">
    <fieldset>
	<input type="text" style="display:none" id="register_id" @if( isset( $register_id ) ) value="{{ $register_id }}" @else value=0 @endif>
      <div class="row" style="margin:0px;">
        <div class="col-lg-12 item" style="padding:0px; margin-bottom:10px;">
          <div class="form-group col-sm-12">
            <label for="title">Date</label>
	    	<input type="text" class="form-control has-feedback-left" id="single_cal3" placeholder="Date" @if( isset($date) ) value="{{ $date }}" @endif name="date" aria-describedby="inputSuccess2Status3"/>          </div>
		  <hr>
          <div class="form-group  col-sm-12">
			<label for="title">EMTs</label>
			<table id="example" data-editing="true" data-paging="false" data-sorting="true" class="display table footable" style="color:black" width="100%"></table>
          </div>
		  <hr>
          <div class="form-group col-sm-12">
			<label for="skills">Recyclage / Skills</label>
			<textarea id="skills" style="min-height:150px;max-height:150px;min-width:100%;max-width:100%;" class="form-control skills-textarea" placeholder="Recyclage / Skills">@if( isset( $skills ) ){{ $skills }}@endif</textarea>
          </div>
          <div class="form-group col-sm-12">
			<label for="additional_participants">Additional Participants</label>
			<select class="form-control js-example-basic-single" multiple name="additional_participant" id="additional_participant">
				@foreach( App\User::select('nickname','id')->where('is_deleted',0)->get() as $user )
					<option @if( isset($participants) && in_array( $user->id , $participants ) ) selected @endif value="{{ $user->id }}">{{ $user->nickname }}</option>
				@endforeach
			</select>
          </div>
		  <hr>
          <div class="form-group  col-sm-12">
			<label for="example2">Shift</label>
			<table id="example2" data-editing="true" data-paging="false" data-sorting="true" class="display table footable" style="color:black" width="100%"></table>
          </div>
          <div class="form-group  col-sm-12">
			<label for="example3">Requests / Problems</label>
			<table id="example3" data-editing="true" data-paging="false" data-sorting="true" class="display table footable" style="color:black" width="100%"></table>
          </div>	
          <div class="form-group  col-sm-12">
			<label for="special_missions">Special Missions</label>
			<textarea id="special_missions" style="min-height:150px;max-height:150px;min-width:100%;max-width:100%;" class="form-control" placeholder="Special Missions">@if( isset( $special_missions) ){{ $special_missions }}@endif</textarea>
          </div>		  
        </div>
      </div>
    </fieldset>
  </div>
</form>
</div>

<div class="container_fluid" style="text-align: center; padding-top: 15px; @if( isset($finished) ) display: none @endif" >
  <button type="button" class="btn btn-danger btn-lg" style="width:40%" onclick="save_all_prompt(0)">Save (Not Final)</button>
  <button type="button" class="btn btn-primary btn-lg" style="width:40%" onclick="save_all_prompt(1)">Submit (Final)</button>
</div>

<footer class="container-fluid text-center">
  <p>Created For Lebanese Red Cross - Batroun Station {{ env('STATION_NUMBER') }}</p>
  <p>Created by Eliie Yaacoub</p>
</footer>

<div class="modal fade" tabindex="-1" role="dialog" style="z-index:99999"aria-labelledby="mySmallModalLabel" aria-hidden="true" id="saveModal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Save</h4>
      </div>
      <div class="modal-body" style="text-align:center">
        <p id="save_text"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" onclick="save_all()" id="modal-btn-si">
          Save
        </button>
        <button type="button" class="btn btn-default" onclick="cancel()" id="modal-btn-si">
          Cancel
        </button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" style="z-index:999999 !important;" id="editor-modal" tabindex="-1" role="dialog" aria-labelledby="editor-title">
	<style scoped>
		/* provides a red astrix to denote required fields - this should be included in common stylesheet */
		.form-group.required .control-label:after {
			content:"*";
			color:red;
			margin-left: 4px;
		}
	</style>
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
				<h4 class="modal-title" id="editor-title">Add Row</h4>
			</div>
			<div class="modal-body">
				<input type="number" id="id" name="id" class="hidden"/>
				<div class="form-group required">
					<label for="name" class="col-sm-3 control-label">Name</label>
					<div class="col-sm-9">
						<select class="form-control js-example-basic-single-1" name="name" id="name">
							@foreach( App\User::select('nickname','id')->where('is_deleted',0)->get() as $user )
								<option value="{{ $user->id }}">{{ $user->nickname }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group">
					<label for="badal" class="col-sm-3 control-label">Badal</label>
					<div class="col-sm-9">
						<select class="form-control js-example-basic-single-2" name="badal" id="badal">
							<option selected value="-1">Nope</option>
							@foreach( App\User::select('nickname','id')->where('is_deleted',0)->get() as $user )
								<option value="{{ $user->id }}">{{ $user->nickname }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group required">
					<label for="shift_status" class="col-sm-3 control-label">Shift Status</label>
					<div class="col-sm-9">
						<select class="form-control js-example-basic-single-ar" name="shift_status" id="shift_status">
							@foreach( App\Http\ORM\ShiftStatus::select('description','id')->get() as $status)
								<option value="{{ $status->id }}">{{ $status->description }}</option>
							@endforeach
						</select>
					</div>
				</div>
				<div class="form-group required">
					<label for="arrival" class="col-sm-3 control-label">Arrival</label>
					<div class="col-sm-9">
						<input type="time" value="16:00:00" class="form-control" id="arrival" name="arrival" placeholder="Arrival" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="departure" class="col-sm-3 control-label">Departure</label>
					<div class="col-sm-9">
						<input type="time" class="form-control" id="departure" name="departure" placeholder="Departure" required>
					</div>
				</div>
				<div class="form-group">
					<label for="checkup" class="col-sm-3 control-label">Checkups</label>
					<div class="col-sm-9">
						<input type="text" min="0" class="form-control" id="checkup" name="checkup" placeholder="Checkup">
					</div>
				</div>
				<div class="form-group">
					<label for="work_description" class="col-sm-3 control-label">Station Work</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" maxlength="200" id="work_description" name="work_description" placeholder="Station Work" required>
					</div>
				</div>
				<div class="form-group">
					<label for="double_s" class="col-sm-3 control-label">Double</label>
					<div class="col-sm-9">
						<input type="checkbox" class="form-control" id="double_s" name="double_s" placeholder="Double">
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">Save changes</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" style="z-index:999999 !important;" id="editor-modal2" tabindex="-1" role="dialog" aria-labelledby="editor-title2">
	<style scoped>
		/* provides a red astrix to denote required fields - this should be included in common stylesheet */
		.form-group.required .control-label:after {
			content:"*";
			color:red;
			margin-left: 4px;
		}
	</style>
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor2">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
				<h4 class="modal-title" id="editor-title2">Add Row</h4>
			</div>
			<div class="modal-body">
				<input type="number" id="ed2_id" name="id" class="hidden"/>
				<div class="form-group required">
					<label for="topic" class="col-sm-3 control-label">Topic</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="topic" name="topic" placeholder="Topic" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="time" class="col-sm-3 control-label">Time</label>
					<div class="col-sm-9">
						<input type="time" class="form-control" id="time" name="time" placeholder="Time" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="comments" class="col-sm-3 control-label">Comments</label>
					<div class="col-sm-9">
						<textarea style="min-height:150px;max-height:150px;min-width:100%;max-width:100%;" class="form-control" id="comments" name="comments" placeholder="Comments"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">Save changes</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>

<div class="modal fade" style="z-index:999999 !important;" id="editor-modal3" tabindex="-1" role="dialog" aria-labelledby="editor-title3">
	<style scoped>
		/* provides a red astrix to denote required fields - this should be included in common stylesheet */
		.form-group.required .control-label:after {
			content:"*";
			color:red;
			margin-left: 4px;
		}
	</style>
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor3">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
				<h4 class="modal-title" id="editor-title2">Add Row</h4>
			</div>
			<div class="modal-body">
				<input type="text" id="ed3_id" name="id" class="hidden"/>
				<div class="form-group required">
					<label for="type" class="col-sm-3 control-label">Type</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="type" name="type" placeholder="Type" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="request" class="col-sm-3 control-label">Request</label>
					<div class="col-sm-9">
						<textarea style="min-height:150px;max-height:150px;min-width:100%;max-width:100%;" class="form-control" id="request" name="request" placeholder="Request"></textarea>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">Save changes</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>

<!-- YAMLI CODE START -->
<script type="text/javascript" src="https://api.yamli.com/js/yamli_api.js"></script>
<script type="text/javascript">
  if (typeof(Yamli) == "object" && Yamli.init( { uiLanguage: "en" , startMode: "onOrUserDefault" , zIndexBase : "9999999999" } ))
  {
    Yamli.yamlify( "skills", { settingsPlacement: "inside" } );
    Yamli.yamlify( "special_missions", { settingsPlacement: "inside" } );
    Yamli.yamlify( "topic", { settingsPlacement: "inside" } );
    Yamli.yamlify( "comments", { settingsPlacement: "inside" } );
    Yamli.yamlify( "type", { settingsPlacement: "inside" } );
    Yamli.yamlify( "request", { settingsPlacement: "inside" } );
    Yamli.yamlify( "work_description", { settingsPlacement: "inside" } );
	
  }
</script>

<script>

function save_all_prompt(type) {
  if(type == 0) {

    $('#save_text').text('Are you sure you want to save your progress?');
  }else{

    $('#save_text').text('Are you sure you want to submit your progress?\nYou will no longer be able to change the results');
  }
  $('#modal-btn-si').attr("onClick", "save_all('"+type+"');" );
  $('#saveModal').modal('show');
}

function cancel() {
 $('#saveModal').modal('hide'); 
}

function save_all(type) {
  
	if( $('#single_cal3').val() == undefined || $('#single_cal3').val() == null || $('#single_cal3').val() == "" ) {
		
		alert("Please fill Date");
		return;
	}

	var data = {};
	data.id = $('#register_id').val();
	data.date = $('#single_cal3').val();
	data.emts = [];
	$.each(ft.rows.all, function(i, row){
		data.emts.push({
			arrival: row.val().arrival,
			checkup: row.val().checkup,
			departure: row.val().departure,
			id: row.val().id,
			shift_status_id: row.val().shift_status_id,
			user_id: row.val().user_id,
			work_description: row.val().work_description,
			name : row.val().name,
			shift_status : row.val().shift_status,
			double_s : row.val().double_s,
			badal: row.val().badal
		});
	});
	data.topics = [];
	$.each(ft2.rows.all, function(i, row){
		data.topics.push({
			topic: row.val().topic,
			time: row.val().time,
			comments: row.val().comments,
			id: row.val().id
		});
	});
	data.requests = [];
	$.each(ft3.rows.all, function(i, row){
		data.requests.push({
			request: row.val().request,
			type: row.val().type,
			id: row.val().id
		});
	});
	data.skills = $('#skills').val().trim();
	data.additional_participant = $('#additional_participant').select2('val');
	data.special_missions = $('#special_missions').val().trim();
	data.finished = type;

  $('#saveModal').modal('hide');

  var formData = {
      "_token": "{{ csrf_token() }}",
      "form" : data
  }


$.ajax({
        url:'register',
        dataType:'json',
        type: 'post',
        data: formData,
        success:function(response){
	    
if(response.Status == 0) { alert(response.Message); window.location.href = '/all_register'; }
else
{ alert("Success");window.location.href = '/all_register'; }

        },
	error:function() {
	alert("Failed");
	}
    });

}

var $modal = $('#editor-modal'),
	$editor = $('#editor'),
	$editorTitle = $('#editor-title'),
	ft = FooTable.init('#example', {
		"empty" : "No EMTs yet",
		"columns": [
        		{ "name": "name", title: "Name" },
        		{ "name": "badal_name", title: "Badal" },
        		{ "name": "shift_status", title: "Shift Status" },
        		{ "name": "arrival", title: "Arrival Team" },
        		{ "name": "departure", "title": "Leaving Time" },
			{ "name": "checkup", "title": "Checkups" },
			{ "name": "work_description", "title": "Station Work" },
        		{ "name": "id", title: "Action" ,"visible":false },
			{ "name": "user_id", title: "Action" ,"visible":false },
			{ "name": "shift_status_id", title: "Action" ,"visible":false },
			{ "name": "double_s", title: "Double" ,"visible":false },
			{ "name": "badal", title: "Badal" ,"visible":false }
		],
		@if( isset($emts) && count($emts) > 0 )
		rows : [
			@foreach( $emts as $emt )
				{ 
					"name" : "{{ $emt->name }}" ,
					"arrival" : "{{ $emt->arrival }}",
					"departure" : "{{ $emt->departure }}",
					"checkup" : "{{ $emt->checkup }}",
					"id" : "{{ $emt->id }}",
					"user_id" : "{{ $emt->user_id }}",
					"work_description" : "{{ $emt->work_description }}",
					"shift_status" : "{{ $emt->shift_status}}",
					"shift_status_id" : "{{ $emt->shift_status_id}}",
					"double_s" : "{{ $emt->double_s }}",
					"badal" : "{{ $emt->badal }}",
					"badal_name" : "{{ $emt->badal_name }}"
				},
			@endforeach
		],
		@endif
		editing: {
			enabled: true,
			addRow: function(){
				$modal.removeData('row');
				$editor[0].reset();
				$editor.find('#shift_status').val(null).trigger('change');
				$editor.find('#name').val(null).trigger('change');
				//$editor.find('#work').val(null).trigger('change');
				$editor.find('#badal').val(-1).trigger('change');
				$editorTitle.text('Add a new EMT');
				$modal.modal('show');
			},
			editRow: function(row){
				var values = row.val();
				$editor.find('#id').val(values.id);
				$editor.find('#shift_status').val(values.shift_status_id).trigger('change');
				$editor.find('#arrival').val(values.arrival);
				$editor.find('#departure').val(values.departure);
				$editor.find('#checkup').val(values.checkup);
				$editor.find('#work_description').val(values.work_description);
				$editor.find('#name').val(values.user_id).trigger('change');
				$editor.find('#badal').val(values.badal).trigger('change');
				if(values.double_s==1){
					$editor.find('#double_s').prop('checked',true);
				} else {
					$editor.find('#double_s').prop('checked',false);
				}

				$modal.data('row', row);
				$editorTitle.text('Edit EMT');
				$modal.modal('show');
			},
			deleteRow: function(row){
				if (confirm('Are you sure you want to delete the row?')){
					row.delete();
				}
			}
		}
	}),
	uid = -9999;

$editor.on('submit', function(e){
	if (this.checkValidity && !this.checkValidity()) return;
	e.preventDefault();

	var row = $modal.data('row'),
		values = {
			id: $editor.find('#id').val(),
			name : $editor.find('#name').select2('data')[0].text,
			user_id : $editor.find('#name').select2('data')[0].id,
			shift_status: $editor.find('#shift_status').select2('data')[0].text,
			shift_status_id: $editor.find('#shift_status').select2('data')[0].id,
			arrival: $editor.find('#arrival').val(),
			departure: $editor.find('#departure').val(),
			checkup: $editor.find('#checkup').val(),
			work_description: $editor.find('#work_description').val(),
			double_s : $editor.find('#double_s').prop('checked')?1:0 ,
			badal_name : $editor.find('#badal').select2('data')[0].text=="Nope"?"":$editor.find('#badal').select2('data')[0].text,
			badal : $editor.find('#badal').select2('data')[0].id
		};
		
	if (row instanceof FooTable.Row){
		row.val(values);
	} else {
		ft.rows.add(values);
	}

	$modal.modal('hide');
});

var $modal2 = $('#editor-modal2'),
	$editor2 = $('#editor2'),
	$editorTitle2 = $('#editor-title2'),
	ft2 = FooTable.init('#example2', {
		"empty" : "No Topics yet",
		"columns": [
        		{ "name": "topic", title: "Topic" },
        		{ "name": "time", title: "Time" },
        		{ "name": "comments", title: "Comments" },
        		{ "name": "id", title: "Action" ,"visible":false }
		],
		@if( isset($topics) && count($topics) > 0 )
		rows : [
			@foreach( $topics as $topic )
				{ 
					"id" : "{{ $topic->id}}" ,
					"topic" : "{{ $topic->topic}}",
					"time" : "{{ $topic->time}}",
					"comments" : "{{ $topic->comments}}"
				},
			@endforeach
		],
		@endif
		editing: {
			enabled: true,
			addRow: function(){
				$modal2.removeData('row');
				$editor2[0].reset();
				$editorTitle2.text('Add new');
				$modal2.modal('show');
			},
			editRow: function(row){
				var values = row.val();
				$editor2.find('#ed2_id').val(values.id);
				$editor2.find('#topic').val(values.topic);
				$editor2.find('#time').val(values.time);
				$editor2.find('#comments').val(values.comments.replace('<br>','\n'));

				$modal2.data('row', row);
				$editorTitle2.text('Edit');
				$modal2.modal('show');
			},
			deleteRow: function(row){
				if (confirm('Are you sure you want to delete the row?')){

					row.delete();
				}
			}
		}
	}),
	uid = -9999;

$editor2.on('submit', function(e){
	if (this.checkValidity && !this.checkValidity()) return;
	e.preventDefault();
	var row = $modal2.data('row'),
		values = {
			id: $editor2.find('#ed2_id').val(),
			topic: $editor2.find('#topic').val(),
			time: $editor2.find('#time').val(),
			comments: $editor2.find('#comments').val().replace(/(?:\r\n|\r|\n)/g, '<br>')
		};
		
	if (row instanceof FooTable.Row){
		row.val(values);
	} else {
		ft2.rows.add(values);
	}

	$modal2.modal('hide');
});

var $modal3 = $('#editor-modal3'),
	$editor3 = $('#editor3'),
	$editorTitle3 = $('#editor-title3'),
	ft3 = FooTable.init('#example3', {
		"empty" : "No Requests yet",
		"columns": [
        		{ "name": "request", title: "Request" },
        		{ "name": "type", title: "Type" },
        		{ "name": "id", title: "Action" ,"visible":false }
		],
		@if( isset($requests) && count($requests) > 0 )
		rows : [
			@foreach( $requests as $request )
				{ 
					"id" : "{{ $request->id}}" ,
					"request" : "{{ $request->request}}",
					"type" : "{{ $request->type}}"
				},
			@endforeach
		],
		@endif
		editing: {
			enabled: true,
			addRow: function(){
				$modal3.removeData('row');
				$editor3[0].reset();
				$editorTitle3.text('Add new');
				$modal3.modal('show');
			},
			editRow: function(row){
				var values = row.val();
				$editor3.find('#ed3_id').val(values.id);
				$editor3.find('#request').val(values.request.replace('<br>','\n'));
				$editor3.find('#type').val(values.type);

				$modal3.data('row', row);
				$editorTitle3.text('Edit');
				$modal3.modal('show');
			},
			deleteRow: function(row){
				if (confirm('Are you sure you want to delete the row?')){

					row.delete();
				}
			}
		}
	}),
	uid = -9999;

$editor3.on('submit', function(e){
	if (this.checkValidity && !this.checkValidity()) return;
	e.preventDefault();
	var row = $modal3.data('row'),
		values = {
			id: $editor3.find('#ed3_id').val(),
			request: $editor3.find('#request').val().replace(/(?:\r\n|\r|\n)/g, '<br>'),
			type: $editor3.find('#type').val()
		};
		
	if (row instanceof FooTable.Row){
		row.val(values);
	} else {
		ft3.rows.add(values);
	}

	$modal3.modal('hide');
});

$(document).ready(function() {
	$('.js-example-basic-single-ar').select2({
		placeholder: 'Choose an Option',
		width:'100%',
		dir:'rtl',
		allowClear: true,
		minimumResultsForSearch: Infinity
	});

	$('.js-example-basic-single-1').select2({
		placeholder: 'Choose an Option',
		width:'100%',
		dropdownParent: $('#editor-modal')
	});

	$('.js-example-basic-single-2').select2({
		placeholder: 'Choose an Option',
		width:'100%',
		dropdownParent: $('#editor-modal')
	});

	$('.js-example-basic-single').select2({
		placeholder: 'Choose an Option',
		width:'100%'
	});


	$('#single_cal3').datepicker({
		format : "dd/mm/yyyy"
	});
});

</script>

</body>
</html>