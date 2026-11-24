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
            <h3>{{ $PageTitle }}</h3>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
		<div class="x_title">
                    <h2>Survey search criteria</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
                    <div class="clearfix"></div>
                  </div>
		<div class="x_content">
                    <br>
		    <form id="survey_search">
			<table style="width:100%">
				<tr>
					<td style="width:10%">
						<p style="margin-left:10px;"><b>Date:</b></p>
					</td>
					<td style="width:10%">
						<p style="margin-left:10px;"><b>&nbsp;</b></p>
					</td>					
					<td style="width:30%">
						 <div class="control-group" id="date">
                                                        <div class="controls">
                                                                <div class="input-prepend input-group">
                                                                        <span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
                                                                        <input type="text" style="width: 200px" name="reservation" id="reservation" class="form-control">
                                                        	</div>
                                                 	</div>
                                                 </div>
					</td>
					<td style="width:10%">
						<p style="margin-left:10px;"><b>Filled surveys:</b></p>
					</td>
					<td colspan="2" style="width:10%">
						<input type="checkbox" id="include_filled" name="include_filled" style="margin-bottom:20px;width:20px;height:20px;" checked>
					</td>
				</tr>
				<tr>
					<td style="width:10%">
						<p style="margin-left:10px;"><b>Driver:</b></p>
					</td>
					<td style="width:10%">
						<input type="checkbox" name="include_driver" id="include_driver" onchange="mask_driver()" style="margin-bottom:20px;width:20px;height:20px;" checked>
					</td>
					<td style="width:30%">
						<div>
                                                	<select name="driver" id="driver" data-placeholder="Choose an Driver..." class="form-control standardSelect" tabindex="-1" id="car_number" style="display:none;">
                                                    		<option value="-1" selected>-- All --</option>
								@foreach($drivers as $driver)
								<option value="{{ $driver->id }}">{{ $driver->nickname }}</option>
								@endforeach
                                                	</select>
                                            	</div>
					</td>
					<td style="width:10%">
						<p style="margin-left:10px;"><b>Ambulance:</b></p>
					</td>
					<td style="width:10%">
						<input type="checkbox" name="include_ambulance" id="include_ambulance" onchange="mask_ambulance()" style="margin-bottom:20px;width:20px;height:20px;" checked>
					</td>
					<td style="width:30%">
						<div>
                                                	<select name="ambulance" id="ambulance" data-placeholder="Choose an Ambulance..." class="form-control standardSelect" tabindex="-1" id="car_number" style="display:none;">
                                                    		<option value="-1" selected>-- All --</option>
								@foreach($ambulances as $ambulance)
								<option value="{{ $ambulance->id }}">{{ $ambulance->car_number }}</option>
								@endforeach
                                                	</select>
                                            	</div>
					</td>
				</tr>
			</table>
			</form>
			<div class="ln_solid"></div>
			<div class="form-group">
                        <div class="col-md-6 col-sm-6 col-xs-12">
                          <button class="btn btn-primary" onclick="search()" type="button">Search</button>
                        </div>
                      </div>
						
                  </div>
	</div>
	<div class="x_panel">
		<div class="x_title">
                    <h2>Surveys</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                      <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                      </li>
                    </ul>
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
<a id="hide_me" style="display:none" href="#" target="_blank"></a>

<script src="../build/js/jquery.dataTables.min.js"></script>
<script>

(function ($) {
            $.fn.serlizeFromJSON = function (){
                
                var a = this.serializeArray();

                var o = {};
                $.each(a,function (){
                    if(o[this.name]){
                        if(!o[this.name].push){
                            o[this.name] = [o[this.name]];                    
                        }
                            o[this.name].push(this.value || '');
                        }else{
                            o[this.name] = this.value || '';
                        }
                    });
                    return o;
            }
        })(jQuery);


	function search() {


		var formData = {
                		form : JSON.stringify( $('#survey_search').serlizeFromJSON()),
                		"_token": "{{ csrf_token() }}"
            		}
            
            $.ajax({
                url:'search_survey',
                dataType:'json',
                type: 'post',
                data: formData,
                success:function(response){
                    if(response.Status == 1){
                        fill(response.Data);
                    }else{
                        alert("Something went wrong, Consult IT department");
                    }
                }
            });

	}

	function fill(data) {
		
		user_data = [];
		for(i=0;i<data.length;i++) {
			row = [];
			row.push(data[i]["id"]);
			row.push("");
			row.push(data[i]["user"]);
			row.push(data[i]["driver"]);
			row.push(data[i]["ambulance"]);
			row.push(data[i]["patient"]);
			row.push(data[i]["date"]);
			row.push(data[i]["created"]);
			row.push(data[i]["filled_at"]);
			row.push(data[i]["score"]);
			row.push(data[i]["comments"]);
		user_data.push(row);
		}


		var oTable = $("#example").dataTable();
		oTable.fnClearTable();

		if(user_data.length > 0)
			oTable.fnAddData(user_data);
		else
			alert("No Data");

	}

	function mask_driver() {
		if($('#include_driver').is(':checked')) {
			$('#driver').prop('disabled',false).trigger("chosen:updated");
		}else{
			$('#driver').prop('disabled',true).trigger("chosen:updated");
		}
	}

	function mask_ambulance() {
		if($('#include_ambulance').is(':checked')) {
			$('#ambulance').prop('disabled',false).trigger("chosen:updated");
		}else{
			$('#ambulance').prop('disabled',true).trigger("chosen:updated");
		}
	}

    $(document).ready(function(){
        $(".standardSelect").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%",
            disabled: true
        });


var table = $('#example').DataTable( {
        columns: [
            { title: ""},
            { title: ""},
            { title: "User"},
            { title: "Driver"},
            { title: "Ambulance"},
            { title: "Patient"},
            { title: "Date"},
            { title: "Created"},
            { title: "Filled"},
            { title: "Score"},
            { title: "Comments"}
        ],

        "searching": false,
        "lengthChange": false,
        "pageLength": 15,
        "scrollX": true,
"columnDefs": [ 
            {
                "targets": 1,
                "visible": false,
                "searchable": false
            },
        {
            "targets":0,
            "data": null,
            "defaultContent": "<button type='button' style='margin-top: 15px;' class='btn btn-info add'>Investigate</button>"
        } ]
    } );

    $('#example tbody').on('click', 'button', function () {
 
        var table = $('#example').DataTable();
        var data = table.row( $(this).parents('tr') ).data();
	
	$("#hide_me").attr("href","/view_survey?Id=" + data[0]);
	document.getElementById("hide_me").click();
//	        window.location = "/view_survey?Id=" + data[0];        
    });
    });
    


</script>
@endsection
