@extends('layouts.master')
@section('content')

<!--style-->
<style>
.row-active{
	background-color: green;
}
.row-inactive{
	background-color: red;
}
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

/* The Close Button */
.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}

    .danger {
	background-color:red;
    }

    .warning {
	background-color:orange;
    }

    .normal {
	background-color:green;
    }

    /*.footable-delete {
	display:none !important;
    }*/

    .modal-backdrop {
	display:none !important;
    }

</style>
<!--Style-->

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
                    <h2>Build Car Checkup</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="form-group required">
				<label for="car-number" class="control-label">Title</label>
				<div>
					<input type="text" class="form-control" id="id" name="id" placeholder="id" style="display:none" value="{{$id}}">
					<input type="text" class="form-control" id="car-number" name="car-number" placeholder="Title" required value="{{$car_number}}">
				</div>
		    </div>
                </div>

                <div class="x_title">
                    <h2>Build Car Checkup</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table id="example" data-editing="true" class="display" style="color:black" width="100%"></table>
                </div>
		<div>
                        <button type="button" class="btn btn-success btn-lg" onclick="reset()">Reset</button>
                        <button type="button" class="btn btn-success btn-lg" onclick="submit()">Submit</button>	
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
        <h4 class="modal-title">Saving</h4>
      </div>
      <div class="modal-body" id="modal_bd">
        <p>Please wait, this might take a couple seconds.<img id="loading-image" src="images/spinner.gif" alt="Loading..." /></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" disabled id="close_btn" >Close</button>
      </div>
    </div>

  </div>
</div>

<!--<div id="myModal" class="modal">


  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Edit Material Count</h3>
    <hr>
    <p><span id="material_name"></p>
    <p>Current Count : <span id="material_count"></p>
    <br/>
    <div class="input-group">
        <input type="number" value="1" min="0" class="form-control" id="new_count" aria-label="Text input with dropdown button">
        <div class="input-group-btn">
            <label class="btn">
                <input id="remove_cb" type="radio" name="gender" checked value="remove"> Remove
            </label>
            <label class="btn">
                <input type="radio" name="gender" value="add"> Add
            </label>
        </div>
    </div>
    <br/>
    <button type="submit" onclick="me()" class="btn btn-success">Submit</button>
  </div>

</div>

<div id="myModalError" class="modal">


  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>Error</h3>
    <hr>
    <p><span id="error_span"></span></p>
  </div>

</div> -->

<div class="modal fade" style="z-index:99 !important;" id="editor-modal-no-edit" tabindex="-1" role="dialog" aria-labelledby="editor-title">
	<style scoped>
		/* provides a red astrix to denote required fields - this should be included in common stylesheet */
		.form-group.required .control-label:after {
			content:"*";
			color:red;
			margin-left: 4px;
		}
	</style>
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-no-edit">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
				<h4 class="modal-no-edit" id="editor-title-type">Error</h4>
			</div>
			<div class="modal-body">
				<div class="form-group required">
					<label for="type" class="control-label">Images cannot be edited. Please remove, and add again.</label>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Ok</button>
			</div>
		</form>
	</div>
</div>


<div class="modal fade" style="z-index:99 !important;" id="editor-modal-type" tabindex="-1" role="dialog" aria-labelledby="editor-title">
	<style scoped>
		/* provides a red astrix to denote required fields - this should be included in common stylesheet */
		.form-group.required .control-label:after {
			content:"*";
			color:red;
			margin-left: 4px;
		}
	</style>
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-type">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
				<h4 class="modal-title" id="editor-title-type">Add Row</h4>
			</div>
			<div class="modal-body">
				<div class="form-group required">
					<label for="type" class="col-sm-3 control-label">Type</label>
					<div class="col-sm-9">
						<select class="form-control js-example-basic-single-1" name="type" id="type">
							@foreach( App\Http\ORM\CarCheckupQuestionType::select('id','description')->where('is_deleted',0)->get() as $question )
								<option value="{{ $question->id }}">{{ $question->description }}</option>
							@endforeach
						</select>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-primary">Go</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>


<div class="modal fade" style="z-index:99 !important;" id="editor-modal-Number" tabindex="-1" role="dialog" aria-labelledby="editor-title">
	<style scoped>
		/* provides a red astrix to denote required fields - this should be included in common stylesheet */
		.form-group.required .control-label:after {
			content:"*";
			color:red;
			margin-left: 4px;
		}
	</style>
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-Number">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
				<h4 class="modal-title" id="editor-title-Number">Add Row</h4>
			</div>
			<div class="modal-body">
				<input type="number" id="id" name="id" class="hidden"/>
				<div class="form-group required">
					<label for="title-en" class="col-sm-3 control-label">Title English</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="title-en" name="title-en" placeholder="Title English" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="title-ar" class="col-sm-3 control-label">Title Arabic</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="title-ar" name="title-ar" placeholder="Title Arabic" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="min" class="col-sm-3 control-label">Minimum</label>
					<div class="col-sm-9">
						<input type="number" min="0" class="form-control" id="min" name="min" placeholder="Minimum Value" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="max" class="col-sm-3 control-label">Maximum Value</label>
					<div class="col-sm-9">
						<input type="number" min="1" class="form-control" id="max" name="max" required placeholder="Maximum Value">
					</div>
				</div>
				<div class="form-group required">
					<label for=default" class="col-sm-3 control-label">Default Value</label>
					<div class="col-sm-9">
						<input type="number" class="form-control" id="default" name="default" required placeholder="Default Value">
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

<div class="modal fade" style="z-index:99 !important;" id="editor-modal-Text" tabindex="-1" role="dialog" aria-labelledby="editor-title">
	<style scoped>
		/* provides a red astrix to denote required fields - this should be included in common stylesheet */
		.form-group.required .control-label:after {
			content:"*";
			color:red;
			margin-left: 4px;
		}
	</style>
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-Text">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
				<h4 class="modal-title" id="editor-title-Test">Add Row</h4>
			</div>
			<div class="modal-body">
				<input type="number" id="id" name="id" class="hidden"/>
				<div class="form-group required">
					<label for="title-en" class="col-sm-3 control-label">Title English</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="title-en" name="title-en" placeholder="Title English" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="title-ar" class="col-sm-3 control-label">Title Arabic</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="title-ar" name="title-ar" placeholder="Title Arabic" required>
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

<div class="modal fade" style="z-index:99 !important;" id="editor-modal-TrueFalse" tabindex="-1" role="dialog" aria-labelledby="editor-title">
	<style scoped>
		/* provides a red astrix to denote required fields - this should be included in common stylesheet */
		.form-group.required .control-label:after {
			content:"*";
			color:red;
			margin-left: 4px;
		}
	</style>
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-TrueFalse">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
				<h4 class="modal-title" id="editor-title-TrueFalse">Add Row</h4>
			</div>
			<div class="modal-body">
				<input type="number" id="id" name="id" class="hidden"/>
				<div class="form-group required">
					<label for="title-en" class="col-sm-3 control-label">Title English</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="title-en" name="title-en" placeholder="Title English" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="title-ar" class="col-sm-3 control-label">Title Arabic</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="title-ar" name="title-ar" placeholder="Title Arabic" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="default" class="col-sm-3 control-label">Default Value</label>
					<div class="col-sm-9">
						<input type="checkbox" class="form-control" id="checkbox" name="checkbox" placeholder="Default Value">
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

<div class="modal fade" style="z-index:99 !important;" id="editor-modal-Image" tabindex="-1" role="dialog" aria-labelledby="editor-title">
	<style scoped>
		/* provides a red astrix to denote required fields - this should be included in common stylesheet */
		.form-group.required .control-label:after {
			content:"*";
			color:red;
			margin-left: 4px;
		}
	</style>
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-Image">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
				<h4 class="modal-title" id="editor-title-Image">Add Row</h4>
			</div>
			<div class="modal-body">
				<input type="number" id="id" name="id" class="hidden"/>
				<div class="form-group required">
					<label for="title-en" class="col-sm-3 control-label">Title English</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="title-en" name="title-en" placeholder="Title English" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="title-ar" class="col-sm-3 control-label">Title Arabic</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="title-ar" name="title-ar" placeholder="Title Arabic" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="image" class="col-sm-3 control-label">Image</label>
					<div class="col-sm-9">
						<input type="file" name="image" id="image" accept="image/*">
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

<div class="modal fade" style="z-index:99 !important;" id="editor-modal-Range" tabindex="-1" role="dialog" aria-labelledby="editor-title">
	<style scoped>
		/* provides a red astrix to denote required fields - this should be included in common stylesheet */
		.form-group.required .control-label:after {
			content:"*";
			color:red;
			margin-left: 4px;
		}
	</style>
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-Range">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
				<h4 class="modal-title" id="editor-title-Range">Add Row</h4>
			</div>
			<div class="modal-body">
				<input type="number" id="id" name="id" class="hidden"/>
				<div class="form-group required">
					<label for="title-en" class="col-sm-3 control-label">Title English</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="title-en" name="title-en" placeholder="Title English" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="title-ar" class="col-sm-3 control-label">Title Arabic</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="title-ar" name="title-ar" placeholder="Title Arabic" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="min" class="col-sm-3 control-label">Minimum Value</label>
					<div class="col-sm-9">
						<input type="number" min="0" class="form-control" id="min" name="min" placeholder="Minimum Value" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="max" class="col-sm-3 control-label">Maximum Value</label>
					<div class="col-sm-9">
						<input type="number" min="1" class="form-control" id="max" name="max" required placeholder="Maximum Value">
					</div>
				</div>
				<div class="form-group required">
					<label for="step" class="col-sm-3 control-label">Step</label>
					<div class="col-sm-9">
						<input type="number" min="1" class="form-control" id="step" name="step" required placeholder="Step">
					</div>
				</div>
				<div class="form-group required">
					<label for=default" class="col-sm-3 control-label">Default Value</label>
					<div class="col-sm-9">
						<input type="number" class="form-control" id="default" name="default" required placeholder="Default Value">
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



<div class="modal fade" style="z-index:99 !important;" id="editor-modal-LeftRight" tabindex="-1" role="dialog" aria-labelledby="editor-title">
	<style scoped>
		/* provides a red astrix to denote required fields - this should be included in common stylesheet */
		.form-group.required .control-label:after {
			content:"*";
			color:red;
			margin-left: 4px;
		}
	</style>
	<div class="modal-dialog" role="document">
		<form class="modal-content form-horizontal" id="editor-LeftRight">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">x</span></button>
				<h4 class="modal-title" id="editor-title-LeftRight">Add Row</h4>
			</div>
			<div class="modal-body">
				<input type="number" id="id" name="id" class="hidden"/>
				<div class="form-group required">
					<label for="title-en" class="col-sm-3 control-label">Title English</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="title-en" name="title-en" placeholder="Title English" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="title-ar" class="col-sm-3 control-label">Title Arabic</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="title-ar" name="title-ar" placeholder="Title Arabic" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="default" class="col-sm-3 control-label">Default Value</label>
					<div class="col-sm-9">
						<input type="checkbox" class="form-control" id="checkbox" name="checkbox" placeholder="Default Value">
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


<link type="text/css" rel="stylesheet" href="dd/css/footables/footable.css">
<script src="dd/js/footable/footable.js"></script>
<script>

var ft;

function reset() {
	location.reload();
}

function submit() {
	var data = {};

	var title = $('#car-number').val();

	if( title == null || title == "" ) {
		alert("Please set Title");
		return;	
	}
	data.title = title;
	data.id = $('#id').val();
	data.questions = [];
 
	$.each(ft.rows.all, function(i, row){
		data.questions.push({
			id: row.val().id,
			title_en: row.val().title_en,
			title_ar: row.val().title_ar,
			type: row.val().type,
			min: row.val().min,
			max: row.val().max,
			step: row.val().step,
			default: row.val().default,
			source: row.val().source
		});
	});

    	//Serialize the form
    	var formData = {
        	"_token": "{{ csrf_token() }}",
        	"form" : data
    	};

	$('#myModal').modal('show');

	$.ajax({
        	url:'build_car_checkup',
        	dataType:'json',
        	type: 'post',
        	data: formData,
        	success:function(response){
	    
			if(response.Status == 0)  {
				var msg = response.Message;
			} else {
				var msg = "Saved Succesfully";
			}
			$('#modal_bd').empty();
			$($.parseHTML("<p>"+msg+"</p>")).appendTo('#modal_bd');
			$('#close_btn').prop('disabled', false);


        	}, error:function() {
			$('#modal_bd').empty();
			$($.parseHTML("<p>Failed</p>")).appendTo('#modal_bd');
			$('#close_btn').prop('disabled', false);
		}	
	});

}

$(document).ready(function() {

var edited = [];

var $modal_type  = $('#editor-modal-type');
var $editor_type = $('#editor-type');
var $editorTitle_type = $('#editor-title-type');

var $modal_no_edit = $('#editor-modal-no-edit');

$editor_type.on('submit', function(e){
	if (this.checkValidity && !this.checkValidity()) return;
	e.preventDefault();

	if( $editor_type.find('#type').val() == 1 ) {
		$modal_1.removeData('row');
		$editor_1[0].reset();
		$editorTitle_1.text('Add a new row');
		$modal_1.modal('show');
	} else if( $editor_type.find('#type').val() == 2 ) {
		$modal_2.removeData('row');
		$editor_2[0].reset();
		$editorTitle_2.text('Add a new row');
		$modal_2.modal('show');
	} else if( $editor_type.find('#type').val() == 3 ) {
		$modal_3.removeData('row');
		$editor_3[0].reset();
		$editorTitle_3.text('Add a new row');
		$modal_3.modal('show');
	} else if( $editor_type.find('#type').val() == 4 ) {
		$modal_4.removeData('row');
		$editor_4[0].reset();
		$editorTitle_4.text('Add a new row');
		$modal_4.modal('show');
	} else if( $editor_type.find('#type').val() == 5 ) {
		$modal_5.removeData('row');
		$editor_5[0].reset();
		$editorTitle_5.text('Add a new row');
		$modal_5.modal('show');
	} else if( $editor_type.find('#type').val() == 6 ) {
		$modal_6.removeData('row');
		$editor_6[0].reset();
		$editorTitle_6.text('Add a new row');
		$modal_6.modal('show');
	}

	$modal_type.modal('hide');
});


@foreach( App\Http\ORM\CarCheckupQuestionType::select('id','description')->where('is_deleted',0)->get() as $question )

var $modal_{{$question->id}} = $('#editor-modal-{{$question->description}}');
var $editor_{{$question->id}} = $('#editor-{{$question->description}}');
var $editorTitle_{{$question->id}} = $('#editor-title-{{$question->description}}');


@endforeach

ft = FooTable.init('#example', {
		"sorting" : {
			"emabled" : true
		},
		"columns": [
			{ "name": "title_en", title: "Title English" },
			{ "name": "title_ar", title: "Title Arabic" },
			{ "name": "type", title: "Type" },
			{ "name": "id", title: "" , "visible": false },
			{ "name": "min", title: "" ,"visible": false },
			{ "name": "max", title: "" ,"visible": false },
			{ "name": "step", title: "" ,"visible": false },
			{ "name": "default", title: "" ,"visible": false },
			{ "name": "source", title: "" ,"visible": false },
			{ "name": "action", title: "Up/Down", "formatter":function(value){
				var actions = $('<div/>')
				var user_button = ($('<a/>', {'class':'btn__up btn btn-default'})
					.append($('<span/>', {'class': 'glyphicon glyphicon-chevron-up'}))
					.on("click", this, up))
					.appendTo(actions);
					
				var user_button = ($('<a/>', {'class':'btn__down btn btn-default'})
					.append($('<span/>', {'class': 'glyphicon glyphicon-chevron-down'}))
					.on("click", this, down))
					.appendTo(actions);
				return actions;
			}}
		],
		"rows": [
		@forelse( $tableData as $row )
			{ 
				"id" : "{{$row['id'] }}",
				"title_en" : "{{$row['title_en'] }}",
				"title_ar" : "{{$row['title_ar'] }}",
				"type" : "{{$row['type'] }}",
				"min" : "{{$row['min'] }}",
				"max" : "{{$row['max'] }}",
				"step" : "{{$row['step'] }}",
				"default" : "{{$row['default'] }}",
				"source" : "{{$row['source'] }}"
			},
		@empty
			
		@endforelse 
		],
		editing: {
			enabled: true,
			addRow: function(){
				$modal_type.removeData('row');
				$editor_type[0].reset();
				$editorTitle_type.text('Add a new row');
				$modal_type.modal('show');
			},
			editRow: function(row){
				var values = row.val();

				var type = values.type;
				if( type == "Number" ) {

					$editor_1.find('#id').val(values.id);
					$editor_1.find('#title-en').val(values.title_en);
					$editor_1.find('#title-ar').val(values.title_ar);
					$editor_1.find('#min').val(values.min);
					$editor_1.find('#max').val(values.max);
					$editor_1.find('#default').val(values.default);
					$modal_1.data('row' , row);
					$modal_1.modal('show');
				} else if( type == "Text" ) {

					$editor_2.find('#id').val(values.id);
					$editor_2.find('#title-en').val(values.title_en);
					$editor_2.find('#title-ar').val(values.title_ar);
					$modal_2.data('row' , row);
					$modal_2.modal('show');
				} else if( type == "TrueFalse" ) {

					$editor_3.find('#id').val(values.id);
					$editor_3.find('#title-en').val(values.title_en);
					$editor_3.find('#title-ar').val(values.title_ar);
					$editor_3.find('#default').prop('checked', values.default);
					$modal_3.data('row' , row);
					$modal_3.modal('show');
				} else if( type == "Image" ) {

					$modal_no_edit.modal('show');
				} else if( type == "Range" ) {

					$editor_5.find('#id').val(values.id);
					$editor_5.find('#title-en').val(values.title_en);
					$editor_5.find('#title-ar').val(values.title_ar);
					$editor_5.find('#min').val(values.min);
					$editor_5.find('#max').val(values.max);
					$editor_5.find('#default').val(values.default);
					$editor_5.find('#step').val(values.step);
					$modal_5.data('row' , row);
					$modal_5.modal('show');
				} else if( type == "LeftRight" ) {

					$editor_6.find('#id').val(values.id);
					$editor_6.find('#title-en').val(values.title_en);
					$editor_6.find('#title-ar').val(values.title_ar);
					$editor_6.find('#default').prop('checked', values.default);
					$modal_6.data('row' , row);
					$modal_6.modal('show');
				}


			},
			deleteRow: function(row){
				if (confirm('Are you sure you want to delete the row?')){
					row.delete();
				}
			}
		}
	}),
	uid = -9999;

$editor_1.on('submit', function(e){
	if (this.checkValidity && !this.checkValidity()) return;
	e.preventDefault();
	var row = $modal_1.data('row'),
		values = {
			id: $editor_1.find('#id').val(),
			title_en: $editor_1.find('#title-en').val(),
			title_ar: $editor_1.find('#title-ar').val(),
			min: $editor_1.find('#min').val(),
			max: $editor_1.find('#max').val(),
			default: $editor_1.find('#default').val(),
			type : "Number"
		};

	if (row instanceof FooTable.Row){
		row.val(values);
	} else {
		ft.rows.add(values);
	}
	$modal_1.modal('hide');
});

$editor_2.on('submit', function(e){
	if (this.checkValidity && !this.checkValidity()) return;
	e.preventDefault();
	var row = $modal_2.data('row'),
		values = {
			id: $editor_2.find('#id').val(),
			title_en: $editor_2.find('#title-en').val(),
			title_ar: $editor_2.find('#title-ar').val(),
			type : "Text"
		};

	if (row instanceof FooTable.Row){
		row.val(values);
	} else {
		ft.rows.add(values);
	}
	$modal_2.modal('hide');
});

$editor_3.on('submit', function(e){
	if (this.checkValidity && !this.checkValidity()) return;
	e.preventDefault();
	var row = $modal_3.data('row'),
		values = {
			id: $editor_3.find('#id').val(),
			title_en: $editor_3.find('#title-en').val(),
			title_ar: $editor_3.find('#title-ar').val(),
			default: $editor_3.find('#default').is(':checked'),
			type : "TrueFalse"
		};

	if (row instanceof FooTable.Row){
		row.val(values);
	} else {
		ft.rows.add(values);
	}
	$modal_3.modal('hide');
});

$editor_4.on('submit', function(e){
	if (this.checkValidity && !this.checkValidity()) return;
	e.preventDefault();
	var row = $modal_4.data('row');

	var reader = new FileReader();

	reader.onload = async function(event) {

		values = {
			id: $editor_4.find('#id').val(),
			title_en: $editor_4.find('#title-en').val(),
			title_ar: $editor_4.find('#title-ar').val(),
			source: event.target.result,
			type : "Image"
		};

		console.log(event.target);

		if (row instanceof FooTable.Row){
			row.val(values);
		} else {
			ft.rows.add(values);
		}
		$modal_4.modal('hide');
	};
	if( $('#image') != undefined && 
		$('#image')[0] != undefined && 
		$('#image')[0].files != undefined && 
		$('#image')[0].files[0] != undefined ) {

		reader.readAsDataURL( $('#image')[0].files[0] );

	} else {

		$modal_4.modal('hide');
		alert("Failed adding Image");
	}
});


$editor_5.on('submit', function(e){
	if (this.checkValidity && !this.checkValidity()) return;
	e.preventDefault();
	var row = $modal_1.data('row'),
		values = {
			id: $editor_5.find('#id').val(),
			title_en: $editor_5.find('#title-en').val(),
			title_ar: $editor_5.find('#title-ar').val(),
			min: $editor_5.find('#min').val(),
			max: $editor_5.find('#max').val(),
			default: $editor_5.find('#default').val(),
			step: $editor_5.find('#step').val(),
			type : "Range"
		};

	if (row instanceof FooTable.Row){
		row.val(values);
	} else {
		ft.rows.add(values);
	}
	$modal_5.modal('hide');
});

$editor_6.on('submit', function(e){
	if (this.checkValidity && !this.checkValidity()) return;
	e.preventDefault();
	var row = $modal_6.data('row'),
		values = {
			id: $editor_6.find('#id').val(),
			title_en: $editor_6.find('#title-en').val(),
			title_ar: $editor_6.find('#title-ar').val(),
			default: $editor_6.find('#default').is(':checked'),
			type : "LeftRight"
		};

	if (row instanceof FooTable.Row){
		row.val(values);
	} else {
		ft.rows.add(values);
	}
	$modal_6.modal('hide');
});

	function up(e) {
		e.preventDefault();
		var $row = $(this).closest('tr'); 
		var $prerow = $row.prevAll('tr').first();
		if($prerow.length){
			$prerow[0].animate({backgroundColor : 'green'},500);
			//$row.insertBefore($prerow);
			$('html, body').animate({scrollTop: '-=40px'},50);
			var row = FooTable.getRow($row);
			var prerow = FooTable.getRow($prerow);
			row_values = { // create a hash of the editor row values
				id : row.value.id,
				title_en : row.value.title_en,
				title_ar : row.value.title_ar,
				type : row.value.type,
				min : row.value.min,
				max : row.value.max,
				step : row.value.step,
				default : row.value.default,
				source : row.value.source
			};
			prerow_values = { // create a hash of the editor row values
				id : prerow.value.id,
				title_en : prerow.value.title_en,
				title_ar : prerow.value.title_ar,
				type : prerow.value.type,
				min : prerow.value.min,
				max : prerow.value.max,
				step : prerow.value.step,
				default : prerow.value.default,
				source : prerow.value.source
			};

			prerow.val(row_values);
			row.val(prerow_values);
			//ft.o.rows[id].name = "ddd";
			//ft.init();
			console.log("Up row. Name: " + row.value.title_en);
		}
	}

	function down(e) {
		e.preventDefault();
		var $row = $(this).closest('tr'); 
		var $nextrow = $row.nextAll('tr').first();
		if($nextrow.length){
			$nextrow[0].animate({backgroundColor : 'green'},500);
			//$row.insertAfter($nextrow);			
			$('html, body').animate({scrollTop: '+=40px'},50);  
			var row = FooTable.getRow($row);
			var prerow = FooTable.getRow($nextrow);
			//var id = e.target.attributes["data-row"].value - 1;
			row_values = { // create a hash of the editor row values
				id : row.value.id,
				title_en : row.value.title_en,
				title_ar : row.value.title_ar,
				type : row.value.type,
				min : row.value.min,
				max : row.value.max,
				step : row.value.step,
				default : row.value.default,
				source : row.value.source
			};
			next_values = { // create a hash of the editor row values
				id : prerow.value.id,
				title_en : prerow.value.title_en,
				title_ar : prerow.value.title_ar,
				type : prerow.value.type,
				min : prerow.value.min,
				max : prerow.value.max,
				step : prerow.value.step,
				default : prerow.value.default,
				source : prerow.value.source 
			};

			prerow.val(row_values);
			row.val(next_values);
			//ft.o.rows[id].name = "ddd";
			//ft.init();
			console.log("Down row. Name: " + row.value.title-en);  
		}
	}
});

</script>
@endsection
