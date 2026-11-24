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
                            <table id="add_fuel_station" >
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Fuel Station Name :&nbsp;</label>
                                            <div>
                                                <input class="fuel_station" id="fuel_station" name="fuel_station" style="width:100%;padding:4px;font-size:15px;" type="text"/>
                                            </div>
                                        </div>
                                    </td>
				</tr>
				<tr>
                                    <td class="error_td">
                                        <span id="fuel_station_error" class="error"></span>
                                    </td>
                                </tr>
                                <!--Row for emts-->
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label class="control-label col-md-12 col-sm-12 col-xs-12" style="padding-top:5px;padding-right:10px;" >Commercial number / رقم تجاري :&nbsp;</label>
                                            <div>
                                                <input class="commercial_number" id="commercial_number" name="commercial_number" style="width:100%;padding:4px;font-size:15px;" type="text"/>
                                            </div>
                                        </div>
                                    </td>
				</tr>
				<tr>
                                    <td class="error_td">
                                        <span id="commercial_number_error" class="error"></span>
                                    </td>
                                </tr>
            								
                            </table>

                        </form>
                    <div>
                        <button type="button" class="btn btn-success btn-lg" onclick="reset()">Reset</button>
                        <button type="button" class="btn btn-success btn-lg" onclick="submit()">Submit</button>
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
	$('#info_form')[0].reset();	
	$('.error').text('');
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
        url:'add_fuel_station',
        dataType:'json',
        type: 'post',
        data: formData,
        success:function(response){
            if(response.Status == 1){
                alert("Fuel Station Added Succesfully");
                window.location.href="/add_fuel_coupon";
            }else{
                alert(response.Message);
            }
        }
    });
}

function verify() {

    var flag = true;
    //Car number selected
    var fuel_station = $('#fuel_station').val();
    if(fuel_station== null || fuel_station== ""){

        $('#fuel_station_error').text('Set the Fuel Station Name');
        flag = false;
    }

    if(commercial_number == null || commercial_number == ""){

        $('#commercial_number_error').text('Set the Fuel Commercial Number');
        flag = false;
    }


    return flag;
}

</script>
<!-- YAMLI CODE START -->
<script type="text/javascript" src="https://api.yamli.com/js/yamli_api.js"></script>
<script type="text/javascript">
  if (typeof(Yamli) == "object" && Yamli.init( { uiLanguage: "en" , startMode: "onOrUserDefault" } ))
  {
    Yamli.yamlify( "fuel_station", { settingsPlacement: "inside" } );
  }
</script>
<!-- YAMLI CODE END -->

@endsection
