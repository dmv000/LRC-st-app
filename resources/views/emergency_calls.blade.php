@extends('layouts.master')
@section('content')

<link href="../vendors/datetimepicker/datetimepicker.min.css" rel="stylesheet">

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
                    <h2>Emergency Calls</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
					<div class="row">
						<div class="col-md-4">
							<input type="text" class="form-control has-feedback-left" id="from_time" placeholder="From Time">
							<span class="fa fa-clock-o form-control-feedback left" aria-hidden="true"></span>
						</div>
						<div class="col-md-4">
							<input type="text" class="form-control has-feedback-left" id="to_time" placeholder="To Time">
							<span class="fa fa-clock-o form-control-feedback left" aria-hidden="true"></span>
						</div>
						<div class="col-md-4">
							<a href='#' class='btn btn-danger' id="filter"><i class='fa fa-filter'>Filter</i></a>
							<a href='#' class='btn btn-primary' id="add"><i class='fa fa-plus'>Add New Emergency Call</i></a>
						</div>
					</div>
					<div class="clearfix"></div>
					<br/>
                    <table id="example" class="display" style="color:black" width="100%"></table>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="../vendors/datetimepicker/datetimepicker.min.js" type="text/javascript"></script>
<script src="../build/js/jquery.dataTables.min.js"></script>
<script>

var dataSet = [];

$(document).ready(function() {
	
var actionsStr = "<a href='#' class='edit btn btn-danger btn-xs'><i class='fa fa-pencil'>Edit</i></a><a href='#' class='add btn btn-info btn-xs'><i class='fa fa-plus'>Create</i></a>";
@if( Auth::user() && Auth::user()->memberOf('Chef Equipe') )
	actionsStr += "<a href='#' class='delete btn btn-warning btn-xs'><i class='fa fa-trash'>Delete</i></a>";
@endif
table = $('#example').DataTable( {
    data: dataSet,
    aaSorting: [],
    columns: [
        { data : "car_number" , title: "Car Number" },
        { data : "driver" , title: "Driver" },
        { data : "team_leader" , title: "Team Leader" },
        { data : "call_time" , title: "Call Time" },
        { data : "id" , title: "" , visible : false },
        { sortable : false , data : null , title: "Action" , defaultContent : actionsStr },
		
    ]
} );

	$('#example tbody').on( 'click', 'a', function () {
		
		var data = table.row( $(this).parents('tr') ).data();
		
		if( $(this).hasClass('edit') ){
			window.open("emergency_call?form_id=" + data['id'] , "_blank");
		} else if( $(this).hasClass('add') ){
			window.open("add_emergency?from_call=" + data['id'] , "_blank");
		}@if( Auth::user() && Auth::user()->memberOf('Chef Equipe') ) else if( $(this).hasClass('delete') ){
			var formData = {
                form : data['id'],
                "_token": "{{ csrf_token() }}"
            }            
			$.ajax({
				url:'delete_emergency_call',
				dataType:'json',
				type: 'post',
				data: formData,
				success:function(response){
					$('#filter').click();
				},error:function(){
					alert("Failed deleting Emergency Call. Check with IT Support");
				}
			});
		}
		@endif
	} );
	
	$('#from_time').datetimepicker({});
	$('#to_time').datetimepicker({});
	
	$("#from_time").on("dp.change", function (e) {
		$('#to_time').data("DateTimePicker").minDate(e.date);
	});
	$("#to_time").on("dp.change", function (e) {
		$('#from_time').data("DateTimePicker").maxDate(e.date);
	});
	
	$("#from_time").data("DateTimePicker").date(new Date(new Date().getFullYear(),new Date().getMonth(),new Date().getDate()));
	$("#to_time").data("DateTimePicker").date(new Date());
	
	$('#filter').click();

} );

$('#add').on('click',function(){
	window.open("emergency_call");	
});

$('#filter').on('click',function(){
	var from_date = moment($('#from_time').data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm:ss');
	var to_date = moment($('#to_time').data("DateTimePicker").date()).format('YYYY-MM-DD HH:mm:ss');
	if( from_date != "Invlid date" && to_date != "Invalid date" ){
		var formData = {
			form : {
				from_time : from_date,
				to_time : to_date
			},
			"_token": "{{ csrf_token() }}"
		}
            
		$.ajax({
			url:'emergency_calls',
			dataType:'json',
			type: 'post',
			data: formData,
			success:function(response){
				if(response.Status == 1){
					var newResultSet = JSON.parse(response.Data);
					table.clear().rows.add(newResultSet).draw();
				}else{
					alert("Something went wrong, Consult IT department");
				}
			}
		});
	}
});
</script>

@endsection
