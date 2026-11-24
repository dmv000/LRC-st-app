
<!DOCTYPE html>
<head>
  <title>LRC {{ env('STATION_NUMBER') }} Maneuver</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"/>
  <script src="js/jquery.3.3.1.js"> </script>
  <script src="bootstrap/js/bootstrap.min.js"></script>

  <link href="css/select2.css" rel="stylesheet" />
<script src="js/select2.js"></script>

  <style>

.alert-fixed {
    position:fixed; 
    top: 0px; 
    left: 0px; 
    width: 100%;
    z-index:9999; 
    border-radius:0px;
    display: none;

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
  </style>
</head>
<body id="myPage" data-spy="scroll" data-target=".navbar" data-offset="60">

<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<a class="navbar-brand" href="#myPage">{{ $maneuver->title }}  </a>
		</div>
	</div>
</nav>


<!-- Container (Services Section) -->
<div id="services" class="container-fluid bg-grey" style="padding-top: 50px;">
<h2>Begin Maneuver</h2>
<input type="text" id="working_id" style="display: none" value="{{ $maneuver->working_id }}">
<input type="text" id="sections" style="display: none" value="{{ count($maneuver->sections) }}">
<br>
<form>
	<div class="page-active">
		<fieldset @if($maneuver->is_finished == 1) disabled @endif>
			<legend id="legend">Team</legend>
			<div class="row" style="margin:0px;">
				<div class="col-lg-12 item" style="padding:0px; margin-bottom:10px;">
					<div class="form-group">
						<label for="ml">Mission Leader</label>
						<select id="ml" data-placeholder="Choose Mission Leader..." class="chosen-select" @if($maneuver->is_finished == 1) disabled @endif>
							<option value="-1" selected></option>
              @foreach($users as $user)
              <option @if($maneuver->ml == $user->id) selected @endif value="{{ $user->id }}">{{ $user->name . " ( " . $user->nickname . " )" }}</option>
              @endforeach
						</select>
					</div>
				</div>
			</div>
			<div class="row" style="margin:0px;">
				<div class="col-lg-12 item" style="padding:0px; margin-bottom:10px;">
					<div class="form-group">
						<label for="emts">EMTs</label>
						<select multiple id="emts" data-placeholder="Choose EMTs..." class="chosen-select">
              @foreach($users as $user)
              <option value="{{ $user->id }}" @if( in_array($user->id,$maneuver->emts)) selected @endif @if($maneuver->is_finished == 1) disabled @endif >{{ $user->name . " ( " . $user->nickname . " )" }}</option>
              @endforeach
						</select>
					</div>
				</div>
			</div>
<div class="row">
  <div class="col-lg-12">
  	<div class="form-group">
  		<label>Start Time</label>
    <div class="input-group">
      <span class="input-group-btn">
        <button class="btn btn-danger" onclick="start(this)" type="button" @if(strlen($maneuver->start_time) > 0) disabled >Started @else >Start @endif</button>
      </span>
      <input type="text" id="start_time" class="form-control" disabled placeholder="Start Time" value="{{ $maneuver->start_time }}">
    </div><!-- /input-group -->
</div>
  </div><!-- /.col-lg-6 -->
</div><!-- /.row -->
		</fieldset>
	</div>
</form>
</div>


@foreach($maneuver->sections as $section)
<div class="container-fluid bg-grey">
<form id="{{ $section->id }}">
  <input type="text" style="display: none" name="section_id" value="{{ $section->id }}">
	<div class="page-active">
		<fieldset @if($maneuver->is_finished == 1) disabled @endif>
			<legend id="legend">{{ $section->title }}</legend>
				@foreach($section->questions as $question)
				<div class="col-lg-12 item" style="padding:0px;">
					<div class="form-group" style="margin-bottom:0px;">
						@if($question->type == "title")
						<h2>{{ $question->text }}</h2>
						@else
						<span>{{ $question->text }}</span>
						@endif
						@if($question->type == "boolean")
						<div style="text-align: center">
							<label class="radio-inline"><input type="radio" value="yes" name="{{ $question->id }}" @if($question->value == "yes") checked @endif>Yes</label>
							<label class="radio-inline"><input type="radio" value="no" name="{{ $question->id }}" @if($question->value == "no") checked @endif>No</label>
						</div>
						@elseif($question->type == "text")
						<textarea class="form-control" rows="2" id="comment" name="{{ $question->id }}">{{ $question->value }}</textarea>
						@endif
						@if($question->has_comment == 1)
						<textarea class="form-control" rows="2" id="commentt" name="{{ $question->id }}comm">{{ $question->comment }}</textarea>
						@endif
					</div>
				</div>
				@endforeach
        <label for="comments_{{ $section->id }}">Section Comments</label>
        <textarea class="form-control" rows="5" id="comments_{{ $section->id }}" name="comments">{{ $section->comment }}</textarea>
		</fieldset>
	</div>
</form>
</div>
@endforeach


<div class="container_fluid" style="text-align: center; padding-top: 15px; @if($maneuver->is_finished == 1) display: none @endif" >
  <button type="button" class="btn btn-danger btn-lg" style="width:40%" onclick="save_all_prompt(0)">Save (Not Final)</button>
  <button type="button" class="btn btn-primary btn-lg" style="width:40%" onclick="save_all_prompt(1)">Submit (Final)</button>
</div>

<footer class="container-fluid text-center">
  <p>Created For Lebanese Red Cross - Batroun Station {{ env('STATION_NUMBER') }}</p>
  <p>Created by Eliie Yaacoub</p>
</footer>


<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="saveModal">
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

<button onclick="topFunction()" id="myBtn" title="Go to top">Bottom</button>

<div class="alert alert-info alert-fixed" id="alert1">
  <strong>Auto Saving!</strong>.
</div>

<div class="alert alert-success alert-fixed" id="alert2"> 
  <strong>Auto Save Successful!</strong>.
</div>

<div class="alert alert-warning alert-fixed" id="alert3">
  <strong>Auto Save Failed!</strong>.
</div>


<script>

var autoSaving = false;

function topFunction() {
    $("html, body").animate({ scrollTop: $(document).height() }, "slow");
    return false;
}

$(document).ready( function (){
	$(".chosen-select").select2({ width: "100%" });

});

function start(e) {
	$(e).prop('disabled',true);
	$(e).text('Started');
	var d = new Date();
	$('#start_time').val(d.getHours() + ":" + d.getMinutes() + ":" + d.getSeconds());
}

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
  //No verification is required
  //Start collecting information
  //Mission Leader -- Team -- Starting Time -- Id -- Forms
  var data = {};
  data['mission_leader'] = $('#ml').val();
  data['emts'] = $('#emts').val().join(",");
  data['start_time'] = $('#start_time').val();
  data['id'] = $('#working_id').val();
  data['sections'] = [];
  data['finished'] = type;

  $('form').each( function( index ) {
    if(index > 0) {

      data['sections'].push(getFormObj( $(this).attr('id') ));      
    }
  });

  for(var i=1;i <= $('#sections').val(); i++) {
    data['sections'].push(getFormObj(i));
  }

  $('#saveModal').modal('hide');

  var formData = {
      "_token": "{{ csrf_token() }}",
      "form" : data
  }
            
    $.ajax({
        url:'maneuver',
        dataType:'json',
        type: 'post',
        data: formData,
        success:function(response){
	    $('#alert1').hide();
            if(response.Status == 1){

		if ( autoSaving == true ) {

			$('#alert2').show({
				duration : 2000,
				complete : function() {

					setTimeout( function () {

						$('#alert2').hide();
					}, 2000);
				}
			});
		}else {

	                alert("Saved Succesfully");
	                window.location.href = response.Link;
		}
            }else{

		if ( autoSaving == true ) {

			$('#alert3').show({
				duration : 2000,
				complete : function() {

					setTimeout( function () {

						$('#alert3').hide();
					}, 2000);
				}
			});
		} else {

	                alert(response.Message);
		}

            }
	    autoSaving = false;
        },
	error:function() {
	    $('#alert1').hide();
	    autoSaving = false; 

	$('#alert3').show({
		duration : 2000,
		complete : function() {

			setTimeout( function () {

				$('#alert3').hide();
			}, 2000);
		}
	});

	}
    });

}

function getFormObj(formId) {
    var formObj = {};
    var inputs = $('#'+formId).serializeArray();
    $.each(inputs, function (i, input) {
        formObj[input.name] = input.value;
    });
    return formObj;
}

setInterval(function() {
	// your code goes here...
	//Auto Save
	autoSaving = true;
	$('#alert1').show({
		duration : 400,
		complete : function() {
			
			setTimeout( function () {

				save_all(0);
			}, 2000);
		}
	});
	
}, 2 * 60 * 1000); // 2 * 60 * 1000 milsec

</script>

</body>
</html>
