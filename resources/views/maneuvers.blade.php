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
            <h3>View Maneuvers</h3>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Maneuvers</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <fieldset>
                        <div class="control-group">
                            <div class="x_content">
                                <table id="example" class="display" width="100%">
				</table>
                            </div>
                        </div>
                    </fieldset>
                    <div class="clearfix"></div>
                </div>       
            </div>
        </div>
    </div>
</div>


<script src="dd/js/footable/footable.js"></script>
<script>

$(document).ready(function() {


	$('#example').footable({
		"columns": [
			{ "name": "id", "title": "ID", "visible": false },
			{ "name": "title", "title": "Title" },
			{ "name": "id2" , "title" : "Action" , "formatter": function (value) {
                
                 		var actions = $('<div/>')
                    var str = '<a class="btn btn-default" target="_blank" href="/maneuver?man_id=' + value + '">';
                    var user_button = ($(str)
                    	.append($('<span>View</span>')))
                      .appendTo(actions);

@if(Auth::user()->memberOf('Maneuver Admin') || Auth::user()->memberOf('Head of Station'))
                    var str2 = '<a class="btn btn-default" target="_blank" href="build_maneuver?man_id=' + value + '">';
                    var user_button2 = ($(str2)
                    	.append($('<span>Edit</span>')))
                      .appendTo(actions); 

                    var str3 = '<a class="btn btn-default" href="#" onclick="duplicate(' + value + ')">';
                    var user_button = ($(str3)
                    	.append($('<span>Duplicate</span>')))
                      .appendTo(actions); 
@endif
                                          
                    return actions;
                    //'<span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></a>'
                    }}
		],
		"rows": [
			@foreach($man as $m)
			{ "id" : {{ $m->id }} , "id2" : {{ $m->id }} , "title" : "{{ $m->title }}" },
			@endforeach
		]
	});
});

function duplicate(id){

	alert("Duplicating " + id);

	var formData = {
			"_token": "{{ csrf_token() }}",
			"form" : id
	}

	$.ajax({
		url:'duplicate_maneuver',
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

}
    
</script>
@endsection
