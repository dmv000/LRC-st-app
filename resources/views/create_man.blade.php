<!DOCTYPE html>
<head>
  	<title>LRC {{ env('STATION_NUMBER') }} Maneuver</title>
  	<meta charset="utf-8">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css"/>
	<link rel="stylesheet" href="css/jquery.ui.css"/>

	<script src="../vendors/jquery/dist/jquery.min.js"></script>
	<script src="bootstrap/js/bootstrap.min.js"></script>
	<script src="js/jquery.ui.js"></script>

	<link href="css/select2.css" rel="stylesheet" />
	<script src="js/select2.js"></script>

<style>


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

<body id="myPage" data-spy="scroll" data-target=".navbar" data-offset="60">

<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container" style="margin-left:10px;">
		<div class="navbar-header">
			<a class="navbar-brand" href="#myPage">
        Build Maneuver
      </a>
		</div>
	</div>
</nav>

<div id="services" class="container-fluid bg-grey" style="padding-top: 50px;">
<h2>Begin Maneuver</h2>
<input type="text" id="man_id" style="display: none" value="{{ $id }}">
<br>
<form>
  <div class="page-active">
    <fieldset>
      <div class="row" style="margin:0px;">
        <div class="col-lg-12 item" style="padding:0px; margin-bottom:10px;">
          <div class="form-group">
            <label for="title">Title</label>
            <input type="text" id="title" class="form-control" value="{{ $title }}"placeholder="Title">
          </div>
        </div>
      </div>
    </fieldset>
  </div>
</form>
</div>

<div id="sections" class="container-fluid bg-grey">
<input type="text" id="man_id" style="display: none" >
<br>
<form>
<div class="page-active">
<fieldset id="add_sections_here">
	{!! $content !!}
</fieldset>
<div style="text-align: center;">
<button type="button" class="btn btn-danger btn-lg" style="width:40%" onclick="add_section(this)">Add New Section</button>
</div>
</div>
</form>
</div>

<div class="container_fluid" style="text-align: center; padding-top: 15px;" >
  <button type="button" class="btn btn-danger btn-lg" style="width:40%" onclick="save_all()">Save</button>
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

<script>

$('textarea').bind('click', function(){
    $(this).focus();
});

var seq = 0;

function add_section(e) {
	var str = 	'{!! $sec_str !!}';
	$(e).parent().parent().find('#add_sections_here').append(str);
}

function add_question(e) {
  var str = '{!! $q_str !!}';
  $(e).parent().parent().find('ul').append( str.split('type____').join('type' + seq++) );
  $(e).parent().parent().find('ul').sortable();
  $(e).parent().parent().find('ul').disableSelection();
}

function validate(e) {
	var par = $(e).parent().parent();
	var value = $(e).val();
	if(value == null || value == "" || value.trim() == "") {
		//Invalid
		par.find('.err2').text('Content cannot be Empty');
		par.addClass('has-error');
		par.removeClass('no-error');
		return false;
	}else{
		par.find('.err2').text('');
		par.removeClass('has-error');
		par.addClass('no-error');
		return true;
	}
}

function validate_sec(e) {
	var par = $(e).parent().parent().parent().parent();
	var value = $(e).val();
	if(value == null || value == "" || value.trim() == "") {
		//Invalid
		par.find('.err1').text('Section title cannot be Empty');
		par.addClass('sec-error');
		par.removeClass('no-sec-error');
		return false;
	}else{
		par.find('.err1').text('');
		par.removeClass('sec-error');
		par.addClass('no-sec-error');
		return true;
	}
}

function remove_s(e){
	$(e).parent().parent().parent().parent().remove();
}

function remove_q(e){
	$(e).parent().parent().remove();
}

function hide_q(e){
	var par = $(e).parent().parent();

	var already_hidden = par.find('.hide_q').css('display') == "none" ? true : false;
	if(already_hidden) {
		par.find('.keep').text('Text');
		par.find('.hide_q').show('fast'); 
	}else{
		par.find('.keep').text( par.find('#cont1').val() != "" ? par.find('#cont1').val().substring(0,20) : "Text");		
		par.find('.hide_q').hide('fast');
	}
}

function hide_s(e){
	var par = $(e).parent().parent().parent().parent();

	var already_hidden = par.find('.hide_s').css('display') == "none" ? true : false;
	if(already_hidden) {
		par.find('.keep_s').text('Section Title');
		par.find('.hide_s').show('fast'); 
	}else{
		par.find('.keep_s').text( par.find('.replace_s').val() != "" ? par.find('.replace_s').val().substring(0,20) : "Section Title");			
		par.find('.hide_s').hide('fast');
	}
}



function topFunction() {
    $("html, body").animate({ scrollTop: $(document).height() }, "slow");
    return false;
}

$(document).ready( function (){
	$(".chosen-select").select2({ width: "100%" });


    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
 




});

function save_all_prompt() {
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

function save_all() {
//
	var flag = true;

	$('.replace_s').each( function() {
		
		flag = validate_sec( $(this) ) && flag;
	});

	$('.cont').each( function() {
	
		flag = validate( $(this) ) && flag;
	});

	if(flag == true) {

		var data = {};
		data['id'] = $('#man_id').val();
		data['title'] = $('#title').val();
		data['sections'] = [];
		$('.section').each( function() {

			var sec = {};
			sec['id'] = $(this).find('.sec_id').val();
			sec['title'] = $(this).find('.replace_s').val();
			sec['questions'] = [];
			$(this).find('li').each( function() {

				var q = {};
				q['id'] = $(this).find('.q_id').val();
				q['text'] = $(this).find('.cont').val();
				q['type'] = $(this).find('input[type="radio"]:checked').val();
				q['has_comment'] = $(this).find('.checkbox').is(':checked') == true ? 1 : 0;
				sec['questions'].push( q );
			});
			data['sections'].push( sec );
		});

		var formData = {
			"_token": "{{ csrf_token() }}",
			"form" : data
		}

		$.ajax({
			url:'build_maneuver',
			dataType:'json',
			type: 'post',
			data: formData,
			success:function(response){

				if(response.Status == 1){

					alert("Saved Succesfully");
					window.location.href = response.Link;
				}else{

					alert(response.Message);
				}
			}
		});

	}else {
	alert("Please Complete filling first");
	}

}

function getFormObj(formId) {
    var formObj = {};
    var inputs = $('#'+formId).serializeArray();
    $.each(inputs, function (i, input) {
        formObj[input.name] = input.value;
    });
    return formObj;
}
</script>

</body>
</html>
