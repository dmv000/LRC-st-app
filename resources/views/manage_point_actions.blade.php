@extends('layouts.master')
@section('content')
<link href="../jtable.2.4.0/themes/metro/blue/jtable.min.css" rel="stylesheet" type="text/css" />
<link href="../css/jquery.ui.css" rel="stylesheet" type="text/css" />
<script src="../vendors/jquery/dist/jquery-ui.min.js"></script>
<script src="/jtable.2.4.0/jquery.jtable.min.js" type="text/javascript"></script>

<div class="right_col" role="main">
    <div class="page-title">
        <div class="title_left">
            <h2>{{ $page_title }}</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>System Points</h2>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div id="tableContainer"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {

        $('#tableContainer').jtable({
            title: 'System Points',
            actions: {
                listAction: '/list_point_actions',
                createAction: function(postData) {
                    postData += "&_token={{ csrf_token() }}";
                    return $.Deferred(function($dfd) {
                        $.ajax({
                            url: '/add_point_action',
                            type: 'POST',
                            dataType: 'json',
                            data: postData,
                            success: function(data) {
                                $dfd.resolve(data);
                            },
                            error: function() {
                                $dfd.reject();
                            }
                        });
                    });
                },
                updateAction: function(postData) {
                    postData += "&_token={{ csrf_token() }}";
                    return $.Deferred(function($dfd) {
                        $.ajax({
                            url: '/update_point_action',
                            type: 'POST',
                            dataType: 'json',
                            data: postData,
                            success: function(data) {
                                $dfd.resolve(data);
                            },
                            error: function() {
                                $dfd.reject();
                            }
                        });
                    });
                },
            },
            fields: {
                id: {
                    key: true,
                    list: false
                },
                name: {
                    title: 'Name',
                    width: '80%'
                },
                points: {
                    title: 'Points'
                }
            }
        });

        $('#tableContainer').jtable('load', {
            "_token": "{{ csrf_token() }}"
        });

    });
</script>
@endsection