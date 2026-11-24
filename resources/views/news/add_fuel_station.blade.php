@extends('layouts.master')
@section('content')

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
                    <h2>Information/???????</h2>
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
@endsection
