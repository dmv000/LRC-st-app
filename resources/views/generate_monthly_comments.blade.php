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
            <h3>{{ $page_title }}</h3>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Information/معلومات</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                        <form id="info_form">
                            <table id="add_fuel_station" style="width: 500px;">
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Year : &nbsp;</label>
                                            <div>
						<?php $year = date('Y'); ?>
                                                <select class="form-control standardSelect" name="year" id="year" style="display:none;" tabindex="-1" data-placeholder="Choose a Year...">
                                                    <option value="-1"></option>
                                                    @for($i = $year - 1; $i <= $year + 1; $i++)
                                                    <option @if($i == $year) selected @endif value=" {{ $i }} ">{{ $i }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </td>
				</tr>
				<tr>
                                    <td class="error_td">
                                        <span id="year_error" class="error"></span>
                                    </td>
                                </tr>
                                <!--Row for emts-->
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Month : &nbsp;</label>
                                            <div>
						<?php $month = date('m'); ?>
                                                <select class="form-control standardSelect" name="month" id="month" style="display:none;" tabindex="-1" data-placeholder="Choose a Month...">
                                                    <option value="-1"></option>
                                                    @for($i = 1; $i <= 12; $i++)
                                                    <option @if($i == $month - 1 ) selected @endif value=" {{ $i }} ">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </td>
				</tr>
				<tr>
                                    <td class="error_td">
                                        <span id="month_error" class="error"></span>
                                    </td>
                                </tr>
            								
                            </table>

                        </form>
                    <div>
			<br/>
			<br/>
                        <button type="button" class="btn btn-success btn-lg" onclick="reset()">Reset</button>
                        <button type="button" class="btn btn-success btn-lg" onclick="submit()">Generate Reports</button>
                        </div>

                </div>
            </div>

                    </div>
        </div>
        <br />
    </div>
</div>


<script>

function reset() {	
	$('.error').text('');
	$('.standardSelect').val(-1).trigger('chosen:updated');
}



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

function post(path, params, method) {
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);

            form.appendChild(hiddenField);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

function submit() {

    $('.error').text('');
    var flag = true;
    flag = verify();
    if(!flag) return;
    
    //Serialize the form
    var data = $('#info_form').serlizeFromJSON();

    var formData = {
        "_token": "{{ csrf_token() }}",
        "form" : JSON.stringify(data)
    }
    post('/{{ $report_url }}',formData);
   /* $.ajax({
        url:'generate_fuel_report',
        dataType:'json',
        type: 'post',
        xhrFields: {
	responseType: 'blob'
        },
        data: formData,
        success:function(response){
            if(response == "Error"){
                alert("An Error Has Occured. Please Consult IT");
            }else{
		var a = document.createElement('a');
		var url = window.URL.createObjectURL(response);
		a.href = url;
		a.download = 'FuelReport.zip';
		a.click();
		window.URL.revokeObjectURL(url);
            }
        }
    });*/
}

function verify() {

    var flag = true;
    //Car number selected
    var year = $('#year').val();
    var month = $('#month').val();
    if(year == null || year == "-1"){

        $('#year_error').text('Set the Year');
        flag = false;
    }

    if(month == null || month == "-1"){

        $('#month_error').text('Set the Fuel Month');
        flag = false;
    }


    return flag;
}

$(document).ready(function() {


    $(document).ready(function(){
        $(".standardSelect").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%",
            disabled: true
        });
    });


});


</script>

@endsection
