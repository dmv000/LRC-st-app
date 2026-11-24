@extends('layouts.master')
@section('content')
<style>
	.cnt {
	    font-size: 24px;
	    font-weight: 700;
	    line-height: 1.65857;
    	    margin: 0 0 0 10px;
    	    z-index: 5;
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
                    <h2>User Surveys</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <fieldset>
                        <div class="control-group">
                            <div class="x_content">
                                @forelse ($surveys as $survey )
					<div class="tile-stats">
    						<!--<div class="icon"><a href="#"><i class="fa fa-caret-square-o-right"></i></a></div>-->
                  				<div class="cnt">Driver : {{ $survey->driver_name }}</div>
                  				<h3><a href="{{env('SURVEY_INTERNAL_URL')}}/driver_survey?id={{ $survey->guid }}">Fill survey</a></h3>
                			</div>
				@empty
					<div class="tile-stats">
                  				<div class="count">Congratulations</div>
                  				<h3><a href="#">You have completed all your surveys</a></h3>
                			</div>
				@endforelse
                            </div>
                        </div>
                    </fieldset>
                    <div class="clearfix"></div>
                </div>
	    </div>
	</div>
    </div>
</div>

@endsection
