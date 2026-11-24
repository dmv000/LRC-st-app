@extends('layouts.master')
@section('content')

<link href="../jtable.2.4.0/themes/metro/blue/jtable.min.css" rel="stylesheet" type="text/css" />
<link href="../css/jquery.ui.css" rel="stylesheet" type="text/css" />
<script src="../vendors/jquery/dist/jquery-ui.min.js"></script>
<script src="/jtable.2.4.0/jquery.jtable.min.js" type="text/javascript"></script>

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
                <div class="x_content">
                    <div class="well" style="overflow: auto">
                        <div>
                            <label class="control-label col-md-1 col-sm-1 col-xs-1" style="padding-top:5px;padding-right:10px;">Station:&nbsp;</label>
                            <div class="col-md-3 col-sm-3 col-xs-3">
                                <select data-placeholder="Choose the Station..." class="select2_group form-control standardSelect" id="station" style="display:none;margin-bottom:20px;" tabindex="-1">
                                    <option value="-1">Choose the Station...</option>
                                    @foreach ($stations as $station)
                                    <option value="{{ $station->id }}">
                                        {{ $station->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <label class="control-label col-md-1 col-sm-1 col-xs-1" style="padding-top:5px;padding-right:10px;">Village:&nbsp;</label>
                            <div class="col-md-3 col-sm-3 col-xs-3">
                                <input type="text" maxlength="200" class="form-control has-feedback-left" id="village" placeholder="Choose the Village...">
                            </div>
                            <div class="col-md-3 col-sm-3 col-xs-12">
                                <button onclick="search()" type="button" class="btn btn-primary btn-block">Search</button>
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
                    <h2>Villages</h2>
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

        var stations = {
            @foreach($stations as $station)
            {{ $station->id }}: "{{ $station->name }}",
            @endforeach
        }

        $('#tableContainer').jtable({
            title: 'Villages',
            paging: true,
            pageSize: 10,
            actions: {
                listAction: '/get_villages',
                createAction: function(postData) {
                    postData += "&_token={{ csrf_token() }}";
                    return $.Deferred(function($dfd) {
                        $.ajax({
                            url: '/add_village',
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
                            url: '/update_village',
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
                station: {
                    title: 'Station',
                    width: '50%',
                    options: stations
                },
                village: {
                    title: 'Village Name',
                    width: '50%'
                }
            }
        });

        $('#tableContainer').jtable('load', {
            "_token": "{{ csrf_token() }}"
        });
    });

    function search() {

        $('#tableContainer').jtable('load', {
            station: $("#station").val(),
            village: $("#village").val(),
            "_token": "{{ csrf_token() }}"
        });
    }
</script>
@endsection