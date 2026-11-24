@extends('layouts.master')
@section('content')

<!--style-->
<style>
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
                    <h2>All Material</h2>@if(Auth::user()->memberOf('Equipment Admin') || Auth::user()->memberOf('Head of Station') )<button style="float:right;" type="submit" onclick="download()" class="btn btn-success">Export</button>@endif
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table id="example" data-filtering="true" @if(Auth::user()->memberOf('Equipment Admin') || Auth::user()->memberOf('Head of Station') ) data-editing="true" @endif data-paging="true" data-sorting="true" class="display" style="color:black" width="100%"></table>
                </div>


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

<div class="modal fade" style="z-index:99 !important;" id="editor-modal" tabindex="-1" role="dialog" aria-labelledby="editor-title">
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
					<label for="description" class="col-sm-3 control-label">Description</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="description" name="description" placeholder="Description" required>
					</div>
				</div>
				<div class="form-group required">
					<label for="count" class="col-sm-3 control-label">Count</label>
					<div class="col-sm-9">
						<input type="number" min="0" class="form-control" id="count" name="count" placeholder="count" required>
					</div>
				</div>
				<div class="form-group" required>
					<label for="criticalCount" class="col-sm-3 control-label">Critical Count</label>
					<div class="col-sm-9">
						<input type="number" min="1" class="form-control" id="criticalCount" name="criticalCount" required placeholder="Critical Count">
					</div>
				</div>
				<div class="form-group">
					<label for="criticalCount" class="col-sm-3 control-label">Comment</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" id="comment" name="comment" placeholder="Comment">
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

$(document).ready(function() {

var edited = [];

var $modal = $('#editor-modal'),
	$editor = $('#editor'),
	$editorTitle = $('#editor-title'),
	ft = FooTable.init('#example', {
"columns": [
        		{ "name": "description", title: "Name" },
        		{ "name": "count", title: "Count" },
        		{ "name": "criticalCount", title: "Critical Count" },
        		{ "name": "percentage", "title": "Percentage" },
			{ "name": "comment", "title": "Comment" },
        		{ "name": "id", title: "Action" ,"visible":false }/*"formatter": function (value) {
                
                    var actions = $('<div/>')
                    var str = '<a class="btn btn-default" href="#" onclick="edit(this)">';
                    var user_button = ($(str)
                    	.append($('<span>Edit</span>')))
                      .appendTo(actions);
                                          
                    return actions;
                    //'<span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></a>'
                    }}*/
		],
		"rows": [
			@foreach($materials as $m)
			{ 
				"id" : {{ $m->id }} ,
				"description" : "{{ $m->description }}" , 
				"count" : "{{ $m->count }}",
				"criticalCount" : "{{ $m->criticalCount }}",
				"percentage" : {
					"options": {
						"classes" : 	
							@if( (float) str_replace("%","",$m->percentage)  <= 100 ) "danger" 
							@elseif( (float) str_replace("%","",$m->percentage) <= 200 ) "warning" 
							@else "normal"
							@endif
					},
					"value": "{{ $m->percentage }}"
				},
				"comment" : "{{ $m->comment }}"
			},
			@endforeach
		],
		editing: {
			enabled: true,
			addRow: function(){
				$modal.removeData('row');
				$editor[0].reset();
				$editorTitle.text('Add a new row');
				$modal.modal('show');
			},
			editRow: function(row){
				var values = row.val();
				$editor.find('#id').val(values.id);
				$editor.find('#description').val(values.description);
				$editor.find('#count').val(values.count);
				$editor.find('#criticalCount').val(values.criticalCount);
				$editor.find('#comment').val(values.comment);

				$modal.data('row', row);
				$editorTitle.text('Edit row');
				$modal.modal('show');
			},
			deleteRow: function(row){
				if (confirm('Are you sure you want to delete the row?')){

				var formData = {
					"_token": "{{ csrf_token() }}",
					"form" : row.val().id

				}

				$.ajax({
					url:'allMaterial',
					dataType:'json',
					type: 'delete',
					data: formData,
					success:function(response){

						if(response.Status == 1){
							alert("Deleted Succesfully");
							row.delete();
						}else{

							alert(response.Message);
						}
					},
					failure:function() {
						alert("An Error Has Occured, please contact IT");
					}
				});


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
			description: $editor.find('#description').val(),
			count: $editor.find('#count').val(),
			criticalCount: $editor.find('#criticalCount').val(),
			comment: $editor.find('#comment').val()
		};

	//Try saving :)
	var formData = {
			"_token": "{{ csrf_token() }}",
			"form" : values
	}

	$.ajax({
		url:'allMaterial',
		dataType:'json',
		type: 'post',
		data: formData,
		success:function(response){

			if(response.Status == 1){
				values.id = response.id ;

				var am = parseFloat( ( values.count / values.criticalCount ) * 100 ).toFixed(2);
				var c = "normal";

				if(am <= 100 ) c = "danger";
				else if (am <= 200) c = "warning";

				values.percentage = {"options": {"classes" : c},"value": am + "%"};

				//values.percentage = {  };parseFloat( ( values.count / values.criticalCount ) * 100 ).toFixed(2) + "%";
				if (row instanceof FooTable.Row){
						row.val(values);
				} else {
						ft.rows.add(values);
				}
				alert("Saved Succesfully");
			}else{

				alert(response.Message);
			}
		}
	});

	$modal.modal('hide');
});


});

function download() {
          
    var date = new Date();
    var d = '' + date.getFullYear() + '_' + date.getMonth() + '_' + date.getDate() + '_' + date.getHours() + '_' + date.getMinutes() + '_' + date.getSeconds();

    var csv = FooTable.get('#example').toCSV(true);
	
    var lines = csv.split("\n");
    var out = "";
    
    $.each(lines, function(index,value) { 
	
	val = value.split(",");
	out += val[0] + "," + val[1] + "\n";
    });
    //lines.each(function () { alert("line"); });
    /*  console.log ("csv: " + csv);*/
    var hiddenElement = document.createElement('a');
    hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(out);
    hiddenElement.target = '_blank';
    hiddenElement.download = 'Material_export_' + d + '.csv';
    hiddenElement.click();
    document.body.removeChild(hiddenElement);
}

</script>
@endsection
