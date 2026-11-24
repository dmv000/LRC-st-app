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
                    <h2>Unfilled User Surveys</h2>
		    <button style="float:right;" type="submit" onclick="download()" class="btn btn-success">Export</button>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table id="example" class="display" style="color:black" width="100%"></table>
                </div>
            </div>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Unfilled User Surveys Ratio</h2>
                    <div class="clearfix"></div>
                </div>
				<div class="x_content">
					<fieldset>
						<div class="control-group">
							<table>
								<tr>
									<td>
										<div class="form-group">
											<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Date Range&nbsp;</label>
											<div class="col-md-9 col-sm-9 col-xs-12">
												<form class="form-horizontal">
													<fieldset>
														<div class="control-group">
															<div class="controls">
																<div class="input-prepend input-group" style="width: 400px">
																	<span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
																	<input type="text" name="reservation2" id="reservation2" class="form-control">
																</div>
															</div>
														</div>
													</fieldset>
												</form>
											</div>
										</div>
									</td>
									<td>
										<div class="form-group">
											<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;">&nbsp;</label>
											<div class="col-md-9 col-sm-9 col-sx-12">
												<button style="margin-bottom:10px" onclick="getReport()" type="button" class="btn btn-primary btn-min-width">Get Unfilled Survey Ratio Report</button>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td class="error-td">
										<span id="start_date_error2" class="error"></span>
									</td>
								</tr>
							</table>
						</div>
					</fieldset>
					<div class="clearfix"></div>
				</div>  
            </div>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Reset Unfilled User Surveys</h2>
                    <div class="clearfix"></div>
                </div>
				<div class="x_content">
					<fieldset>
						<div class="control-group">
							<table>
								<tr>
									<td>
										<div class="form-group">
											<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Date Range&nbsp;</label>
											<div class="col-md-9 col-sm-9 col-xs-12">
												<form class="form-horizontal">
													<fieldset>
														<div class="control-group">
															<div class="controls">
																<div class="input-prepend input-group" style="width: 400px">
																	<span class="add-on input-group-addon"><i class="glyphicon glyphicon-calendar fa fa-calendar"></i></span>
																	<input type="text" name="reservation3" id="reservation3" class="form-control">
																</div>
															</div>
														</div>
													</fieldset>
												</form>
											</div>
										</div>
									</td>
									<td>
										<div class="form-group">
											<label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;">&nbsp;</label>
											<div class="col-md-9 col-sm-9 col-sx-12">
												<button style="margin-bottom:10px" onclick="resetUnfilled()" type="button" class="btn btn-primary btn-min-width">Reset Unfilled Surveys</button>
											</div>
										</div>
									</td>
								</tr>
								<tr>
									<td class="error-td">
										<span id="start_date_error3" class="error"></span>
									</td>
								</tr>
							</table>
						</div>
					</fieldset>
					<div class="clearfix"></div>
				</div>  
            </div>
        </div>
    </div>
</div>


<script src="../build/js/jquery.dataTables.min.js"></script>
<script>

var dataSet = [
@for($i=0;$i<count($users);$i++)
[
    "{{$users[$i]['name']}}",
        "{{$users[$i]['nickname']}}",
        "{{$users[$i]['count']}}"
],
@endfor
];

$(document).ready(function() {
var table = $('#example').DataTable( {
    data: dataSet,
    pageLength: 20,
    columns: [
        { title: "Name" },
        { title: "Nickname" },
        { title: "Unfilled Surveys" },
    ]
} );

$('#reservation2').daterangepicker({drops:'up'});
$('#reservation3').daterangepicker({drops:'up'});

} );


function download() {
                         
  var date = new Date();
  var d = '' + date.getFullYear() + '_' + date.getMonth() + '_' + date.getDate() + '_' + date.getHours() + '_' + date.getMinutes() + '_' + date.getSeconds();

  var text = "";
  $('#example tbody tr').each( function() {
            
        var tds = $( this ).find('td').slice(0,3);
                     
	text += tds[0].innerHTML + "," + tds[1].innerHTML + "," + tds[2].innerHTML + "\n"; 
  }); 

  var element = document.createElement('a');
  element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text));
  element.setAttribute('download', 'Unfilled_surveys_' + d + '.csv');

  element.style.display = 'none';
  document.body.appendChild(element);

  element.click();

  document.body.removeChild(element);
}



    function getReport(){
		
		var date = $('#reservation2').val().trim();
        var startDate = date.split('-')[0].trim();
        var endDate = date.split('-')[1].trim();

        if (Date.parse(endDate) < Date.parse(startDate)) {
            $("#start_date_error2").text('Start Date must be lower than End Date');
        }else{
            window.open(
				"/unfilledSurveyRatioReport?startDate=" + startDate + "&endDate=" + endDate,
				'_blank'
			);
        }
    }



function resetUnfilled(){
    
    var date = $('#reservation3').val().trim();
    var startDate = date.split('-')[0].trim();
    var endDate = date.split('-')[1].trim();

    if (Date.parse(endDate) < Date.parse(startDate)) {
        $("#start_date_error3").text('Start Date must be lower than End Date');
    }else{

        var data = {
            "_token": "{{ csrf_token() }}",
            "startDate" : startDate,
            "endDate" : endDate
        }
                
        $.ajax({
            url:'resetUnfilledSurveys',
            dataType:'json',
            type: 'post',
            data: data,
            success:function(response){
                if(response.Status == 1){
                    alert("Unfilled Surveys Reset Succesfully");
                }else{
                    alert(response.Message);
                }
            }
        });
    }
}

</script>
@endsection
