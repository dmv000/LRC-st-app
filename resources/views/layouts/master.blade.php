<!DOCTYPE html>
<html lang="en">
  <head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ env('STATION_NUMBER') }} Statistics</title>

    <link rel="stylesheet" href="dd/css/chosen/chosen.min.css">

    <!-- Bootstrap -->
    <link href="../vendors/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <!--<link href="../vendors/nprogress/nprogress.css" rel="stylesheet">-->
    <!-- iCheck -->
    <!--<link href="../vendors/iCheck/skins/flat/green.css" rel="stylesheet">-->

    <!-- bootstrap-progressbar -->
    <!--<link href="../vendors/bootstrap-progressbar/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">-->
    <!-- JQVMap -->
    <!--<link href="../vendors/jqvmap/dist/jqvmap.min.css" rel="stylesheet"/>-->
    <!-- bootstrap-daterangepicker -->
    <link href="../vendors/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../build/css/custom.min.css?v=1.0.2" rel="stylesheet">
<link href="../build/css/jquery.dataTables.min.css" rel="stylesheet">
<link href="../build/css/buttons.dataTables.min.css" rel="stylesheet">
<link href="css/select2.css" rel="stylesheet" />

<style>
/* Paste this css to your style sheet file or under head tag */
/* This only works with JavaScript, 
if it's not present, don't show loader */
.no-js #loader { display: none;  }
.js #loader { display: block; position: absolute; left: 100px; top: 0; }
.se-pre-con {
	position: fixed;
	left: 0px;
	top: 0px;
	width: 100%;
	height: 100%;
	z-index: 9999;
	background: url(images/ambulance.gif) center no-repeat #fff;
}
</style>


  </head>
  <div class="se-pre-con"></div>
  <body class="nav-md">
    <div class="container body">
      <div class="main_container">
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a class="site_title"><i class="fa fa-ambulance"></i> <span>{{ env('STATION_NUMBER') }} Red Cross</span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <!--<img src="images/img.jpg" alt="..." class="img-circle profile_img">-->
		<img class="img-circle profile_img" @if(Auth::check()) src="{{ env('SEC_URL') }}/dashboard/imageByNickname?image={{ Auth::user()->nickname }}" @else src="images/img.jpg" @endif alt="User Avatar" onError="this.onerror=null;this.src='images/img.jpg';">

              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                @if(Auth::check()) <h2>{{ Auth::user()->nickname }}</h2>
<a class="nav-link" href="{{ route('logout') }}" 
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                            <i class="fa fa-power -off"></i>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
@else
<a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}<i class="fa fa-power -off"></i></a>
@endif
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
					@include('includes.sidebar')
            </div>
            <!-- /sidebar menu -->

          </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
          <div class="nav_menu">
				@include('includes.topnav')
          </div>
        </div>

    
<!-- jQuery -->
    <script src="../vendors/jquery/dist/jquery.min.js"></script>

    <script src="../vendors/Chart.js/dist/Chart.min.js"></script>


    <!-- Bootstrap -->
    <script src="../vendors/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- FastClick -->
    <!--<script src="../vendors/fastclick/lib/fastclick.js"></script>-->
    <!-- NProgress -->
    <!--<script src="../vendors/nprogress/nprogress.js"></script>-->
    <!-- Chart.js -->
    <!-- gauge.js -->
    <!--<script src="../vendors/gauge.js/dist/gauge.min.js"></script>-->
    <!-- bootstrap-progressbar -->
    <!--<script src="../vendors/bootstrap-progressbar/bootstrap-progressbar.min.js"></script>-->
    <!-- iCheck -->
    <!--<script src="../vendors/iCheck/icheck.min.js"></script>-->
    <!-- Skycons -->
    <!--<script src="../vendors/skycons/skycons.js"></script>-->
    <!-- Flot -->
    <!--<script src="../vendors/Flot/jquery.flot.js"></script>
    <script src="../vendors/Flot/jquery.flot.pie.js"></script>
    <script src="../vendors/Flot/jquery.flot.time.js"></script>
    <script src="../vendors/Flot/jquery.flot.stack.js"></script>
    <script src="../vendors/Flot/jquery.flot.resize.js"></script>-->
    <!-- Flot plugins -->
    <!--<script src="../vendors/flot.orderbars/js/jquery.flot.orderBars.js"></script>
    <script src="../vendors/flot-spline/js/jquery.flot.spline.min.js"></script>
    <script src="../vendors/flot.curvedlines/curvedLines.js"></script>-->
    <!-- DateJS -->
    <!--<script src="../vendors/DateJS/build/date.js"></script>-->
    <!-- JQVMap -->
    <!--<script src="../vendors/jqvmap/dist/jquery.vmap.js"></script>
    <script src="../vendors/jqvmap/dist/maps/jquery.vmap.world.js"></script>
    <script src="../vendors/jqvmap/examples/js/jquery.vmap.sampledata.js"></script>-->
    <!-- bootstrap-daterangepicker -->
    <script src="../vendors/moment/min/moment.min.js"></script>
    <script src="../vendors/bootstrap-daterangepicker/daterangepicker.js"></script>
	<script src="js/select2.js"></script>

    <script src="../build/js/custom.js?v=1.0.0"></script>
    <script src="dd/js/chosen/chosen.jquery.min.js"></script>


        <!-- /top navigation -->

        @yield('content')

        <!-- footer content -->
        <footer>
          <div class="pull-right">
            Website developped By Eliie Yaacoub
          </div>
          <div class="clearfix"></div>
        </footer>
        <!-- /footer content -->
      </div>
    </div>



    <div class="modal fade" id="globalErrorModal" tabindex="-1" role="dialog" aria-labelledby="globalErrorModalLabel" aria-hidden="true" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title" id="globalErrorModalLabel">Error</h2>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="globalErrorModalBody">
            <span style="color:red" id="globalErrorModalSpan"></span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
          </div>
        </div>
      </div>
    </div>



    <div class="modal fade" id="globalSuccessModal" tabindex="-1" role="dialog" aria-labelledby="globalSuccessModalLabel" aria-hidden="true" data-backdrop="static">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h2 class="modal-title" id="globalSuccessModalLabel">Success</h2>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="globalSuccessModalBody">
            <span style="color:blue" id="globalSuccessModalSpan"></span>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Ok</button>
          </div>
        </div>
      </div>
    </div>


    <!-- Custom Theme Scripts -->
  <script>
  $(window).load(function() {
		// Animate loader off screen
		$(".se-pre-con").fadeOut("slow");;
	});

  function showGlobalError(message) {
    $('#globalErrorModalSpan').html(message);
    $('#globalErrorModal').modal();
  }

  function showGlobalSuccess(message) {
    $('#globalSuccessModalSpan').html(message);
    $('#globalSuccessModal').modal();
  }

  function sendPostRequest(request) {
    return new Promise((resolve, reject) => {
      const formData = {
        data : request.data,
        "_token" : "{{ csrf_token() }}"
      };
      $.ajax({
        url : request.url,
        dataType : !request.dataType ? 'json' : request.dataType,
        type : 'post',
        data : formData,
        success : (data) => resolve(data),
        error : () => reject()
      });
    });
  }
  </script>

  </body>
</html>
