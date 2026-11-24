@extends('layouts.master')
@section('content')
<style>
	table {
		white-space: nowrap;
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
                    <h2>Actions</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
			<button type="button" class="btn btn-primary" onclick="window.location.href = '/build_car_checkup';">Add New Checkup</button>
                </div>

                <div class="x_title">
                    <h2>Mechanical Car Checkups</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table id="example" class="display" width="100%"></table>
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
        <h4 class="modal-title">Checkup Link</h4>
      </div>
      <div class="modal-body" id="modal_bd">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>


<script src="../build/js/jquery.dataTables.min.js"></script>
<script>

function delete_mission(id){
        var formData = {
            id :id,
            "_token": "{{ csrf_token() }}"                        
        }

        $.ajax({
            url:'delete_emergency',
            dataType:'json',
            type: 'post',
            data: formData,
            success:function(response){
                if(response.Status == 1){
                    alert("Urgence Removed Succesfully");
                    location.reload();
                }else {
		    alert(response.Message);
		}
            }
        });
}


$(document).ready(function() {

    var dataSet = [
        @for($i=count($tableData) - 1; $i >= 0 ; $i--)
            [
                    "{{$tableData[$i]["id"]}}",	   
                    "{{$tableData[$i]["car_number"]}}",
                    "{{$tableData[$i]["current_version"]}}",
                    "",
		    "<p>To access the Checkup from station network</p><p>Open <span style='text-decoration:underline;color:blue'>{{env('SURVEY_INTERNAL_URL')}}/car_checkup?car={{$tableData[$i]['car_number']}}#stay</span></p><p>Or scan the below QrCode with your mobile</p><img style='height:300px;' src='data:image/png;base64, {!! base64_encode(\QrCode::format('png')->size(400)->generate(env('SURVEY_INTERNAL_URL') . '/car_checkup?car=' . $tableData[$i]['car_number'] . '#stay' )) !!} '>", 
		    "<p>To access the Checkup from online network</p><p>Open <span style='text-decoration:underline;color:blue'>{{env('SURVEY_EXTERNAL_URL')}}/car_checkup?car={{$tableData[$i]['car_number']}}#stay</span></p><p>Or scan the below QrCode with your mobile</p><img style='height:300px;' src='data:image/png;base64, {!! base64_encode(\QrCode::format('png')->size(400)->generate(env('SURVEY_EXTERNAL_URL') . '/car_checkup?car=' . $tableData[$i]['car_number'] . '#stay' )) !!} '>" 


],
        @endfor
    ];

    var table = $('#example').DataTable( {
        data: dataSet,
        columns: [
            { title: ""},
            { title: "Car Number"},
            { title: "Current Version"},
            { title: "Actions"},
            { title: ""}
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
            },{
                "targets": 4,
                "visible": false,
                "searchable": false
            },
        {
            "targets":3,
            "data": null,
            "defaultContent": "<button type='button' style='margin-top: 15px;' class='btn btn-info link-online'>Get Online Link</button>&nbsp;<button type='button' style='margin-top: 15px;' class='btn btn-info link'>Get Link</button>&nbsp;@if(Auth::user()->memberOf('Drivers Admin'))<button type='button' style='margin-top: 15px;' class='btn btn-info edit'>Edit</button>&nbsp;<button type='button' style='margin-top: 15px;' class='btn btn-info delete'>Delete</button>&nbsp;<button type='button' style='margin-top: 15px;' class='btn btn-info duplicate'>Duplicate</button>@endif"
        } ]
    } );

$('#example tbody').on( 'click', 'button', function () {

	if($(this).hasClass('link')) {
		$('#modal_bd').empty();
        	var data = table.row( $(this).parents('tr') ).data();
		$($.parseHTML(data[4])).appendTo('#modal_bd');
		$('#myModal').modal('show');

	}else if($(this).hasClass('link-online')) {
		$('#modal_bd').empty();
        	var data = table.row( $(this).parents('tr') ).data();
		$($.parseHTML(data[5])).appendTo('#modal_bd');
		$('#myModal').modal('show');

	}else if($(this).hasClass('edit')) {
		
		var data = table.row( $(this).parents('tr') ).data();
		window.open("/build_car_checkup?id=" + data[0]);

	} else if ($(this).hasClass('duplicate')) {
		
		var data = table.row( $(this).parents('tr') ).data();
		var formData = {
        	"_token": "{{ csrf_token() }}",
        	"form" : data[0]
    	};

		$.ajax({
				url:'duplicate_car_checkup',
				dataType:'json',
				type: 'post',
				data: formData,
				success:function(response){
			
					if(response.Status == 0)  {
						var msg = response.Message;
					} else {
						var msg = "Duplicated Succesfully";
					}
					alert(msg);
					location.reload();

				}, error:function() {
					alert("Failed");
				}	
		});
	} else if ($(this).hasClass('delete')) {
		
		var data = table.row( $(this).parents('tr') ).data();
		var formData = {
        	"_token": "{{ csrf_token() }}",
        	"form" : data[0]
    	};

		$.ajax({
				url:'delete_car_checkup',
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
					location.reload();

				}, error:function() {
					alert("Failed");
				}	
		});
	}
} );

} );
</script>
@endsection
