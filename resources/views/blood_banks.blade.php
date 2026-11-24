@extends('layouts.master')
@section('content')



<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h3></h3>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>{{ $page_title }}</h2>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <table id="example" class="display" style="color:black" width="100%"></table>
                </div>


            </div>
        </div>
    </div>
</div>








<script src="../build/js/jquery.dataTables.min.js"></script>
<script>




var dataSet = [
    @for($i=0;$i<count($blood_banks);$i++)
    [
        "{{$blood_banks[$i]['name']}}",
            "{{$blood_banks[$i]['code']}}",
            "{{$blood_banks[$i]['district']}}",
            "{{$blood_banks[$i]['station']}}",
            "{{$blood_banks[$i]['id']}}",
            "{{$blood_banks[$i]['location']}}"
    ],
    @endfor
];

var stationSet = [
    @for($i=0;$i<count($stations);$i++)
    [
        "{{$stations[$i]['id']}}",
            "{{$stations[$i]['name']}}",
            "{{$stations[$i]['district']}}"
    ],
    @endfor
];
var stationSet2 = stationSet;

var dataSet2 = dataSet;
var table;
$(document).ready(function() {
table = $('#example').DataTable( {
    data: dataSet2,
    aaSorting: [],
    columns: [
        { title: "Name" },
        { title: "Code" },
        { title: "District" },
        { title: "Station" },
        { title: "" }
    ],

"columnDefs": [ {
    "targets": 4,
    "data": null,
    "sortable": false,
    "defaultContent": "<button type='button' class='btn btn-info btn-min-width'>Location and More Info</button>"
} ]
} );

$('#example tbody').on( 'click', 'button', function () {
    var data = table.row( $(this).parents('tr') ).data();
    //alert( data[0] +"'s salary is: "+ data[ 2 ] );
    window.location.href = "hos_location?id=" + data[4];

    } );




} );
</script>

@endsection
