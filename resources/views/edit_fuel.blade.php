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
                    <div class="well" style="overflow: auto">
                        <form id="info_form">
		<input type="text" value="{{ $record->id}}" name="id" style="display:none;"/>
                            <table id="add_fuel_log" >
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Car number / السيّارة :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <select class="form-control standardSelect" name="car_number" id="car_number" style="display:none;" tabindex="-1" data-placeholder="Choose an Ambulance...">
                                                    <option value="-1"></option>
                                                    @foreach($cars as $car)
                                                    <option @if($record->car_id == $car->id) selected @endif value=" {{ $car->id }} ">{{ $car->car_number }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
									
									<td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Driver / السائق :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <select class="form-control standardSelect" id="driver" name="driver" style="display:none;" tabindex="-1" data-placeholder="Choose the Driver...">
                                                    <option value="-1"></option>
                                                    @foreach($drivers as $driver)
                                                    <option @if($record->driver_id == $driver->id) selected @endif value="{{ $driver->id }}">{{ $driver->nickname }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
									
									
									
									<td>
                                        <div class="form-group">
                                            <label class="control-label col-md-18 col-sm-18 col-xs-18" style="padding-top:5px" >Army / الجيش :&nbsp;</label>
												<input type="checkbox" class="form-control" id="army"
                                                name="army" @if($record->army) checked @endif>
												</input>
                                        </div>
                                    </td>
									
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span id="car_number_error" class="error"></span>
                                    </td>
									<td class="error_td">
                                        <span id="driver_error" class="error"></span>
                                    </td>
									<td class="error_td">
                                        <span id="army_error" class="error"></span>
                                    </td>
                                </tr>
                                <!--Row for emts-->
                                <tr style="vertical-align:top;">
								
									<td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Date / التاريخ :&nbsp;</label>
                                            <div class="col-md-9 xdisplay_inputx form-group has-feedback">
                                                <input type="text" class="form-control has-feedback-left" name="date" id="single_cal3" placeholder="Date" value=" {{ $date }} " aria-describedby="inputSuccess2Status3">
                                                <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                                <span id="inputSuccess2Status2" class="sr-only">(success)</span>
                                            </div>
                                        </div>
                                    </td>
									
									<td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Fuel Station(Supplier) / المصدر :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <select class="form-control standardSelect" name="fuel_station" id="fuel_station" style="display:none;" tabindex="-1" data-placeholder="Choose a Fuel Station...">
                                                    <option value="-1"></option>
                                                    @foreach($stations as $station)
                                                    <option @if($record->fuel_station_id == $station->id) selected @endif value=" {{ $station->id }} ">{{ $station->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
									
									<td>
                                        <div class="form-group">
                                            <label class="control-label col-md-18 col-sm-18 col-xs-18" style="padding-top:5px;padding-right:10px;" >Fuel Types / نوع الفيول :&nbsp;</label>
                                            <div class="col-md-18 col-sm-18 col-xs-18">
                                                <select class="form-control standardSelect" id="fuel_type" name="fuel_type" style="display:none;" tabindex="-1" data-placeholder="Choose a Fuel Type...">
                                                    <option value="-1"></option>
                                                    @foreach($fuel_types as $fuel_type)
                                                    <option @if($record->fuel_type_id == $fuel_type->id) selected @endif value="{{ $fuel_type->id }}">{{ $fuel_type->description }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </td>
									
									
                                    
									
                                    
                                </tr>
                                <tr>
                                    <td class="error_td">
                                        <span id="date_error" class="error"></span>
                                    </td>
									<td class="error_td">
                                        <span id="fuel_station_error" class="error"></span>
                                    </td>
                                    <td class="error_td">
                                        <span id="fuel_type_error" class="error"></span>
                                    </td>
                                    
                                </tr>
                                <!--Row for times-->
                                <tr>
                                    
									<td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Number / الرقم :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <input name="number" value="{{ $record->coupon_number }}" id="number" style="width:100%;padding:4px;font-size:15px;" type="number" min="0" step="1"/>
                                            </div>
                                        </div>
                                    </td>
									
									<td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Litres / ليتر :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <input id="litres" value="{{ $record->litre }}" name="litres" style="width:100%;padding:4px;font-size:15px;" type="number" min="0" step="1"/>
                                            </div>
                                        </div>
                                    </td>
									
                                </tr>
								<tr>
                                    <td class="error_td">
                                        <span id="number_error" class="error"></span>
                                    </td>
									<td class="error_td">
                                        <span id="litres_error" class="error"></span>
                                    </td>
                                    
                                </tr>
								
								<tr>
                                    
									<td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Price / السعر :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <input id="price" name="price" value="{{ $record->price }}" style="width:100%;padding:4px;font-size:15px;" type="number" min="0" step="1"/>
                                            </div>
                                        </div>
                                    </td>
									
									<td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Kilometrage / كيلومتراج :&nbsp;</label>
                                            <div class="col-md-9 col-sm-9 col-xs-12">
                                                <input name="kms" id="kilo" value="{{ $record->kms }}" style="width:100%;padding:4px;font-size:15px;" type="number" min="0" step="1"/>
                                            </div>
                                        </div>
                                    </td>
									
                                </tr>
								<tr>
                                    <td class="error_td">
                                        <span id="price_error" class="error"></span>
                                    </td>
									<td class="error_td">
                                        <span id="kms_error" class="error"></span>
                                    </td>
                                    
                                </tr>
								
                            </table>
                        </form>
                    </div>
                </div>
            </div>

                
               
            </div>
            <div class="x_panel" id="bottom_panel">
                <div class="x_title">
                    <h2>Actions</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="well" style="overflow: auto">
                        <button type="button" class="btn btn-success btn-lg" onclick="reset()">Reset</button>
                        <button type="button" class="btn btn-success btn-lg" onclick="submit()">Submit</button>
                        </div>
                </div>
            </div>
        </div>
        <br />
    </div>
</div>


<script>

function reset() {
	$('#info_form')[0].reset();	
	$('.standardSelect').val(-1).trigger('chosen:updated');
}

    $(document).ready(function(){
        $(".standardSelect").chosen({
            disable_search_threshold: 10,
            no_results_text: "Oops, nothing found!",
            width: "100%",
            disabled: true
        });
    });


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
            
    $.ajax({
        url:'edit_fuel_coupon',
        dataType:'json',
        type: 'post',
        data: formData,
        success:function(response){
            if(response.Status == 1){
                alert("Coupon Updated Succesfully");
                window.location.href="driver_fuel_logs";
            }else{
                alert(response.Message);
            }
        }
    });
}

function verify() {

    var flag = true;
    //Car number selected
    var car_number = $('#car_number').val();
    if(car_number == -1){

        $('#car_number_error').text('Select an Ambulance');
        flag = false;
    }

    //Driver selected
    var driver = $('#driver').val();
    if(driver == -1){

        $('#driver_error').text('Select a Driver');
        flag = false;
    }  

    //Date selected
    var date = $('#single_cal3').val();
    var timestamp = Date.parse(date);

    if (isNaN(timestamp) == true) {
        $('#date_error').text('Select a valid date');    
        flag = false;
    }

    //Fuel Station selected
    var fuel_station = $('#fuel_station').val();
    if(fuel_station == -1){

        $('#fuel_station_error').text('Please Select a fuel Station');
        flag = false;
    }

    //Fuel Type Error
    var fuel_type = $('#fuel_type').val();
    if(fuel_type == -1){

        $('#fuel_type_error').text('Please select a fuel type');
        flag = false;
    } 

    //Number should be a number
    //Disabled for now
    var number = $('#number').val();
    //if(isNaN(parseInt(number))){
    //
    //   $('#number_error').text('Please neter a valid coupon number');
    //    flag = false;
    //}

    //Same for litres
    var litre = $('#litres').val();
    if(isNaN(parseInt(litre))){

        $('#litres_error').text('Please enter a valid litre count');
        flag = false;
    }

    //Same for price
    var price = $('#price').val();
    if(isNaN(parseInt(price))){

        $('#price_error').text('Please enter a valid price');
        flag = false;
    }

    //Same for kms
    var kms = $('#kilo').val();
    if(isNaN(parseInt(kms))){

        $('#kms_error').text('Please enter a valid Kilometrage');
        flag = false;
    }

    return flag;
}

</script>
@endsection
