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

        <div class="col-md-6 col-sm-6 col-xs-12">
                        <div class="x_panel">
                          <div class="x_title">
                            <h2>Today's Emergencies</h2>
                            <!--<ul class="nav navbar-right panel_toolbox">
                              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                              </li>
                              <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                                <ul class="dropdown-menu" role="menu">
                                  <li><a href="#">Settings 1</a>
                                  </li>
                                  <li><a href="#">Settings 2</a>
                                  </li>
                                </ul>
                              </li>
                              <li><a class="close-link"><i class="fa fa-close"></i></a>
                              </li>
                          </ul>-->
                            <div class="clearfix"></div>
                          </div>
                          <div class="x_content">
                            <canvas id="chart1" style="height:500px;width:500px;"></canvas>
                          </div>
                        </div>
                      </div>

                      <div class="col-md-6 col-sm-6 col-xs-12">
                                      <div class="x_panel">
                                        <div class="x_title">
                                          <h2>Weekly Statistics</h2>
                                          <!--<ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li><a class="close-link"><i class="fa fa-close"></i></a>
                                            </li>
                                        </ul>-->
                                          <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                          <canvas id="chart2" style="height:500px;width:500px;"></canvas>
                                        </div>
                                      </div>
                                    </div>





        </div>
        <br />
    </div>
</div>
<script>

var pieData = {
        labels: [@for($i=1;$i<count($array);$i++)  '{{ $arrayy[$i] }}', @endfor ],
        datasets:[
            {
                backgroundColor:[@for($i=1;$i<count($colors);$i++)  '#000000', @endfor ],
                data: [@for($i=1;$i<count($array);$i++)  {{ $array[$i] }}, @endfor ]
            }
        ]
    };
    //{value:25,label:'rrr'} @for($i=1;$i<count($array);$i++) ,{value: {{ $array[$i] }}, label: '{{ $arrayy[$i] }}' }@endfor};
var context = document.getElementById('chart1').getContext('2d');
var cc = new Chart(context,{type:'pie',data:pieData});//.Pie(pieData);


var ctx = document.getElementById('chart2').getContext('2d');
var myChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ['{{ $days[0] }}' @for($i=1;$i<7;$i++)  ,'{{ $days[$i] }}' @endfor ],
    datasets: [{
      label:'XXX',
      data: ['{{ $emergs[0] }}' @for($i=1;$i<7;$i++)  ,{{ $emergs[$i] }} @endfor ],
      backgroundColor: "rgba(153,255,51,0.4)"
    }]
    },
    options: {
        legend: {
            display: false
        },
        tooltips: {
            callbacks: {
               label: function(tooltipItem) {
                      return tooltipItem.yLabel;
               }
            }
        }
    }
});

myChart.legend.display = false;

</script>
@endsection
