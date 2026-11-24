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
    @foreach($point_actions as $point_action)
        [
            "{{ $point_action['id'] }}",
            "{{ $point_action['name'] }}",
            "{{ $point_action['points'] }}"
        ],
    @endforeach
];

var table;
$(document).ready(function() {

table = $('#example').DataTable( {
    data: dataSet,
    paging : false,
    searching: false,
    columns: [
        { "visible": false },
        { title: "Name" },
        { title: "Points" }
    ]
} );

});
</script>

@endsection
