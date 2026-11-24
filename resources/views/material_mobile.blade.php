@extends('layouts.master')
@section('content')

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3>Mobile Application</h3>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Mobile Access</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content" style="text-align:center;font-size:16px;">
                    <p>To access the Material Checkup from mobile</p>
		    <p>Open <span style="text-decoration:underline;color:blue">{{env('SURVEY_INTERNAL_URL')}}/material</span></p>
		    <p>Or scan the below QrCode with your mobile</p>
		    <img style="height:300px;" src="data:image/png;base64, {!! base64_encode(\QrCode::format('png')->size(400)->generate( env('SURVEY_INTERNAL_URL') . '/material')) !!} ">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
