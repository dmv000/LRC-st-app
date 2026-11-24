@extends('layouts.master')
@section('content')
<link href="../vendors/datetimepicker/datetimepicker.min.css" rel="stylesheet">
<style>
	table {
		white-space: nowrap;
	}
	.det-table {
		border-collapse: collapse;
		margin : auto;
		font-size : 15px;
	}
	table.det-table td {
		padding : 10px;
		border-bottom : 2px solid #8eab24;
		text-align: left;
	}
	table.det-table th {
		padding : 7px;
		border-bottom : 1.5px solid #8eab24;
		text-align: left;
	}
	.name-col{
		color : black;
	}
	.row-success{
		color: green;
	}
	.row-warning:after{
		content:" *";
		color: red;
	}
	.row-warning{
		color: red;
	}
	.x{
		position: absolute;
	}
</style>
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
                    <h2>Mechanical Car Checkups</h2>&nbsp;
					<a href='#' class='btn btn-danger' id="filter"><i class='fa fa-filter'>Filter</i></a>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
					<div class="row">
						<div class="col-md-4">
							<div class="row">
								<div class="col-md-4">
									<label for="from_time">Add Date</label>
								</div>
								<div class="col-md-8">
									<input type="text" class="form-control has-feedback-left" id="from_time" placeholder="From Time">
									<span class="fa fa-clock-o form-control-feedback left" aria-hidden="true"></span>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="row">
								<div class="col-md-4">
									<label for="driver">Driver</label>
								</div>
								<div class="col-md-8">
									<select class="form-control standardSelect" data-placeholder="Choose the Driver..."  id="driver" name="driver" tabindex="-1" style="display:none;">
										<option value="-1">All</option>
										@foreach($drivers as $driver)
										<option value="{{ $driver->id }}">{{ $driver->nickname }}</option>
										@endforeach
									</select>
								</div>
							</div>							
						</div>
						<div class="col-md-4">
							<div class="row">
								<div class="col-md-4">
									<label for="driver">Checkup</label>
								</div>
								<div class="col-md-8">
									<select class="form-control standardSelect" data-placeholder="Choose the Checkup..."  id="checkup" name="checkup" tabindex="-1" style="display:none;">
										<option value="-1">All</option>
										@foreach($checkups as $checkup)
										<option value="{{ $checkup->id }}">{{ $checkup->car_number }}</option>
										@endforeach
									</select>
								</div>
							</div>
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

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog" style="width:1000px;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Details</h4>
      </div>
      <div class="modal-body" id="modal_bd"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<script src="../build/js/jquery.dataTables.min.js"></script>
<script src="../vendors/datetimepicker/datetimepicker.min.js" type="text/javascript"></script>
<script>

$('#filter').on('click',function(){
	var date = $('#from_time').val();
	var from_date = date.split(" - ")[0];
	var to_date = date.split(" - ")[1];
	
	if( from_date != "" && to_date != "" ){
		var formData = {
			form : {
				from_date : from_date,
				to_date : to_date,
				driver : $('#driver').val(),
				checkup : $('#checkup').val()
			},
			"_token": "{{ csrf_token() }}"
		}
            
		$.ajax({
			url:'search_car_checkup_result',
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

var table;
$(document).ready(function() {
	$(".standardSelect").select2({ width: "100%" });
	
	/*$('#from_time').datetimepicker({});
	$('#to_time').datetimepicker({});
	
	$("#from_time").on("dp.change", function (e) {
		$('#to_time').data("DateTimePicker").minDate(e.date);
	});
	$("#to_time").on("dp.change", function (e) {
		$('#from_time').data("DateTimePicker").maxDate(e.date);
	});
	
	$("#from_time").data("DateTimePicker").date(new Date(new Date(new Date().getFullYear(),new Date().getMonth(),new Date().getDate())));
	$("#to_time").data("DateTimePicker").date(new Date());
	
	$('#from_time_2').datetimepicker({});
	$('#to_time_2').datetimepicker({});
	
	$("#from_time_2").on("dp.change", function (e) {
		$('#to_time_2').data("DateTimePicker").minDate(e.date);
	});
	$("#to_time_2").on("dp.change", function (e) {
		$('#from_time_2').data("DateTimePicker").maxDate(e.date);
	});
	
	$("#from_time_2").data("DateTimePicker").date(new Date(new Date().getFullYear(),new Date().getMonth(),new Date().getDate()));
	$("#to_time_2").data("DateTimePicker").date(new Date());*/
	
	$('#from_time').daterangepicker({
		locale : {
			format : 'YYYY-MM-DD'
		}
	});

    var dataSet = [];

    table = $('#example').DataTable( {
        data: dataSet,
        columns: [
            { data : "id", title: ""},
            { data : "car_number", title: "Car Number"},
            { data : "driver", title: "Driver"},
            { data : "filledBy", title: "Filled By"},
            { data : "date", title: "Date"},
            { data : "submitDate", title: "Submit Date"},
            { data : null, title: "Actions"}
        ],
        "searching": false,
        "lengthChange": false,
        "pageLength": 15,
        "scrollX": true,
		"columnDefs": [
            {
                "targets": 0,
                "visible": false,
                "searchable": false
            },
			{
				"targets":6,
				"data": null,
				"defaultContent": "<button type='button' style='margin-top: 15px;' class='btn btn-primary view'>View</button>&nbsp;<button type='button' style='margin-top: 15px;' class='btn btn-danger delete'>Delete</button>&nbsp;"
			}
		]
    } );

$('#example tbody').on( 'click', 'button', function () {

	if ($(this).hasClass('view')) {
		
		var data = table.row( $(this).parents('tr') ).data();
		var formData = {
        	"_token": "{{ csrf_token() }}",
        	"form" : data["id"]
    	};

		$.ajax({
				url:'car_checkup_details',
				dataType:'json',
				type: 'post',
				data: formData,
				success:function(response){
			
					if(response.Status == 0)  {
						var msg = "<p>"+response.Message+"</p>";
					} else {
						var msg = "<table class='det-table'><tr><th class='name-col'>Question</th><th>Value</th></tr>";
						var data = JSON.parse(response.Message);
						data.forEach(function(item, index){
							if(item["type"]==3) {
								var classStr = "";
								if(item["value"]==item["default"]){
									classStr = "row-success";
								} else {
									classStr = "row-warning";
								}
								if(item["value"]=="true"){
									msg += "<tr><td class='name-col "+classStr+"'><b>"+item["name"]+ "</b></td><td class='value-col'>&#9989;</td></tr>"
								} else {
									msg += "<tr><td class='name-col "+classStr+"'><b>"+item["name"]+ "</b></td><td class='value-col'>&#10060;</td></tr>"
								}
							} else if(item["type"]==6) {
								var classStr = "";
								if(item["value"]==item["default"]+","+item["default"]){
									classStr = "row-success";
								} else {
									classStr = "row-warning";
								}
								var content = "";
								if(item["value"].split(",")[0]=="true"){
									content = "Left&nbsp;&#9989;&nbsp;";
								} else {
									content = "Left&nbsp;&#10060;&nbsp;";
								}
								if(item["value"].split(",")[1]=="true"){
									content += "Right&nbsp;&#9989;&nbsp;";
								} else {
									content += "Right&nbsp;&#10060;&nbsp;";
								}
								msg += "<tr><td class='name-col "+classStr+"'><b>"+item["name"]+ "</b></td><td class='value-col'>"+content+"</td></tr>"
							} else if(item["type"]==5) {
								var classStr ="row-success";
								var content = "";
								content = "<input disabled type='range' min='"+item['min']+"' max='"+item['max']+"' value='"+item['value']+"'>("+item['value']+"/"+item['max']+")";
								msg += "<tr><td class='name-col "+classStr+"'><b>"+item["name"]+ "</b></td><td class='value-col'>"+content+"</td></tr>"
							} else if(item["type"]==4) {
								var classStr ="row-success";
								var content = "";
								content = "<div style='position:relative'><img height='200px' class='amb' width='300px' src='"+item['src']+"'>";
								var items = JSON.parse(item["value"]);
								items.forEach(function(mark){
									content += "<img src='images/x.png' height='20px' width:'20px' class='x' style='left: "+(mark[0]+15)+"px; top: "+(mark[1])+"px;' >";
								});
								msg += "<tr><td class='name-col "+classStr+"'><b>"+item["name"]+ "</b></td><td class='value-col'>"+content+"</div></td></tr>"
							} else {
								msg += "<tr><td class='name-col'><b>"+item["name"]+ "</b></td><td class='value-col'>" + item["value"] +"</td></tr>"
							}
						});
					}
					$('#modal_bd').empty();
					$($.parseHTML(msg + "</table>")).appendTo('#modal_bd');
					$('#myModal').modal('show');

				}, error:function() {
					alert("Failed");
				}	
		});
	} else if ($(this).hasClass('delete')) {
		
		var data = table.row( $(this).parents('tr') ).data();
		var formData = {
        	"_token": "{{ csrf_token() }}",
        	"form" : data["id"]
    	};

		$.ajax({
				url:'delete_car_checkup_details',
				dataType:'json',
				type: 'post',
				data: formData,
				success:function(response){
			
					if(response.Status == 0)  {
						var msg = response.Message;
					} else {
						var msg = "Deleted Succesfully";
					}
					alert(msg);
					$('#filter').click();

				}, error:function() {
					alert("Failed");
				}	
		});
	}
} );

} );
</script>
@endsection
