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
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_content">
                    <div class="well" style="overflow: auto">
                        <div>
                            <label class="control-label col-md-1 col-sm-1 col-xs-1" style="padding-top:5px;padding-right:10px;">EMT:&nbsp;</label>
                            <div class="col-md-3 col-sm-3 col-xs-3">
                                <select onchange='loadEmtActions()' data-placeholder="Choose the EMT..." class="select2_group form-control standardSelect" id="emt-id" style="display:none;margin-bottom:20px;" tabindex="-1">
                                    <option value="-1">Choose the EMT...</option>
                                    @foreach ($users as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="control-label col-md-1 col-sm-1 col-xs-1" style="padding-top:5px;padding-right:10px;">Year:&nbsp;</label>
                            <div class="col-md-3 col-sm-3 col-xs-12">
                                <select onchange='loadEmtActions()' data-placeholder="Choose the Year..." class="select2_group form-control standardSelect" id="year" style="display:none;margin-bottom:20px;" tabindex="-1">
                                    @for ($x = -4; $x <= 0; $x++)
                                        <option value="{{ (int)date("Y") + $x }}" @if($x==0) selected @endif>
                                        {{ (int)date("Y") + $x }}
                                        </option>
                                        @endfor
                                </select>
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12">
                                <button onclick="download()" type="button" class="btn btn-primary btn-block">Generate Yearly Report</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr width=100%>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>EMT Points</h2>
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
        $(".standardSelect").select2({
            width: "100%"
        });


        var points = [
            @foreach($points as $point) {
                "DisplayText": "{{ $point->DisplayText }}",
                "Value": "{{ $point->Value }}"
            },
            @endforeach
        ]


        $('#tableContainer').jtable({
            title: 'EMT Points',
            actions: {
                listAction: '/user_points',
                createAction: function(postData) {
                    postData += "&_token={{ csrf_token() }}";
                    return $.Deferred(function($dfd) {
                        user_id = $("#emt-id").val();
                        if (user_id == -1) {
                            $dfd.reject("rrr");
                            return;
                        }
                        postData += "&user_id=" + user_id;
                        $.ajax({
                            url: '/add_point',
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
                            url: '/update_point',
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
                deleteAction: function(postData) {
                    postData['_token'] = "{{ csrf_token() }}";
                    return $.Deferred(function($dfd) {
                        $.ajax({
                            url: '/delete_point',
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
                }
            },
            fields: {
                id: {
                    key: true,
                    list: false
                },
                point_action_id: {
                    title: 'Action',
                    width: '30%',
                    options: points,
                    edit: false
                },
                points: {
                    title: 'Action Points',
                    edit: false,
                    create: false,
                    display: function(data) {
                        return data.record.point_action.points
                    }
                },
                application_date: {
                    title: 'Date',
                    type: 'date',
                    width: '20%'
                },
                application_notes: {
                    title: 'Note',
                    width: '90%'
                }
            }
        });
    });

    function loadEmtActions() {

        $('#tableContainer').jtable('load', {
            userId: $("#emt-id").val(),
            year: $("#year").val(),
            "_token": "{{ csrf_token() }}"
        }, function completed() {
            total = 12;
            $('#tableContainer tbody tr').each(function() {
                points = $(this).find("td:eq(1)").text();
                if (isNaN(points) || points === "") {
                    return;
                }
                total += parseInt(points);
            });

            $('#tableContainer').jtable('addRecord', {
                clientOnly: true,
                record: {
                    point_action: {
                        points: 'Total : ' + total
                    }
                },
                showEditButton: false
            });

            var lastRow = $('#tableContainer tr:last');
            lastRow.find('.jtable-command-button').prop('disabled', true);
        });



    }

    function getFirstDayOfYear() {

        return new Date().getFullYear() + "-01-01";
    }

    function download() {
        let year = $("#year").val();
        var url = "/point_report?year=" + year;
        window.open(url, '_blank');
    }
</script>
@endsection