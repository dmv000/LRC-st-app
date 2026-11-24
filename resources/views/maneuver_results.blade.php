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
            <h3>Maneuver Results</h3>
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
                                <table id="example" class="display" width="100%" data-paging="true">
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
			{ "name": "created_by", "title": "By" },
			{ "name": "mission_leader", "title": "For" },
			{ "name": "date", "title": "Date" },
			{ "name": "link" , "title" : "Action" , "formatter": function (value) {
                
                 		var actions = $('<div/>')
                    var str = '<a class="btn btn-default" target="_blank" href="/maneuver' + value + '">';
                    var user_button = ($(str)
                    	.append($('<span> View / Continue </span>')))
                      .appendTo(actions);
                                          
                    return actions;
                    //'<span class="glyphicon glyphicon-chevron-up" aria-hidden="true"></span></a>'
                    }}
		],
		"rows": [
			@foreach($man as $m)
			{ "id" : {{ $m->id }} 
			  ,"title" : "{{ $m->title }}"
			  ,"created_by" : "{{ $m->created_by }}"
			  ,"mission_leader" : "{{ $m->mission_leader }}"
			  ,"date" : "{{ $m->date }}"
			  ,"link" : "{{ $m->link }}" 
			},
			@endforeach
		]
	});
});
    
</script>
@endsection
