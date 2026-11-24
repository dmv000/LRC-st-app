@extends('layouts.master')
@section('content')

    <!--style-->
    <style>
        /* The Modal (background) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            padding-top: 100px;
            /* Location of the box */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        /* The Close Button */
        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .danger {
            background-color: red;
        }

        .warning {
            background-color: orange;
        }

        .normal {
            background-color: green;
        }

        /*.footable-delete {
                         display:none !important;
                            }*/

        .modal-backdrop {
            display: none !important;
        }

    </style>
    <!--Style-->

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
                        <h2>Kiosk Material</h2>@if (Auth::user()->memberOf('Equipment Admin') || Auth::user()->memberOf('Head of Station'))<button style="float:right;" type="submit" onclick="download()" class="btn btn-success">Export</button>@endif
                        <div class="clearfix"></div>
                    </div>
                    <div class="x_content">
                        <table id="example" data-filtering="true" @if (Auth::user()->memberOf('Equipment Admin') || Auth::user()->memberOf('Head of Station')) data-editing="true" @endif data-paging="true"
                            data-sorting="true" class="display" style="color:black" width="100%"></table>
                    </div>


                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" style="z-index:99 !important;" id="editor-modal" tabindex="-1" role="dialog"
        aria-labelledby="editor-title">
        <style scoped>
            /* provides a red astrix to denote required fields - this should be included in common stylesheet */
            .form-group.required .control-label:after {
                content: "*";
                color: red;
                margin-left: 4px;
            }

        </style>
        <div class="modal-dialog" role="document">
            <form class="modal-content form-horizontal" id="editor">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">x</span></button>
                    <h4 class="modal-title" id="editor-title">Add Row</h4>
                </div>
                <div class="modal-body">
                    <input type="number" id="id" name="id" class="hidden" />
                    <div class="form-group">
                        <label for="description" class="col-sm-3 control-label">Description</label>
                        <div class="col-sm-9">
                            <input disabled type="text" class="form-control" id="description" name="description"
                                placeholder="Description" required>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label for="kiosk" class="col-sm-3 control-label">In kiosk</label>
                        <div class="col-sm-9">
                            <input type="checkbox" min="0" class="form-control" id="kiosk" name="kiosk">
                        </div>
                    </div>
                    <div class="form-group required">
                        <label for="count" class="col-sm-3 control-label">Kiosk Count</label>
                        <div class="col-sm-9">
                            <input type="number" min="0" class="form-control" id="kiosk_count" name="kiosk_count"
                                required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <link type="text/css" rel="stylesheet" href="dd/css/footables/footable.css">
    <script src="dd/js/footable/footable.js"></script>
    <script>
        $(document).ready(function() {

            var edited = [];

            var $modal = $('#editor-modal'),
                $editor = $('#editor'),
                $editorTitle = $('#editor-title'),
                ft = FooTable.init('#example', {
                    "columns": [{
                            "name": "name",
                            title: "Name"
                        },
                        {
                            "name": "kiosk",
                            title: "In Kiosk"
                        },
                        {
                            "name": "kiosk_count",
                            title: "kiosk Count"
                        }, {
                            "name": "id",
                            "visible": false
                        }
                    ],
                    "rows": [
                        @foreach ($materials as $m)
                            {
                            "id" : {{ $m->id }} ,
                            "name" : "{{ $m->name }}" ,
                            "kiosk" : "{{ $m->kiosk == 1 ? 'Yes' : 'No' }}",
                            "kiosk_count" : "{{ $m->kiosk_count }}"
                            },
                        @endforeach
                    ],
                    editing: {
                        enabled: true,
                        allowAdd: false,
                        allowDelete: false,
                        editRow: function(row) {
                            var values = row.val();
                            $editor.find('#id').val(values.id);
                            $editor.find('#description').val(values.name);
                            $editor.find('#kiosk').prop('checked', values.kiosk == "Yes");
                            $editor.find('#kiosk_count').val(values.kiosk_count);

                            $modal.data('row', row);
                            $editorTitle.text('Edit row');
                            $modal.modal('show');
                        }
                    }
                }),
                uid = -9999;

            $editor.on('submit', function(e) {
                if (this.checkValidity && !this.checkValidity()) return;
                e.preventDefault();
                var row = $modal.data('row'),
                    values = {
                        id: $editor.find('#id').val(),
                        kiosk: $editor.find('#kiosk').prop('checked'),
                        kiosk_count: $editor.find('#kiosk_count').val()
                    };

                //Try saving :)
                var formData = {
                    "_token": "{{ csrf_token() }}",
                    "form": values
                }

                $.ajax({
                    url: 'kioskMaterial',
                    dataType: 'json',
                    type: 'post',
                    data: formData,
                    success: function(response) {

                        if (response.Status == 1) {

                            values["kiosk"] = values["kiosk"] == "true" ? "Yes": "No";
                            row.val(values);
                            alert("Saved Succesfully");
                        } else {

                            alert(response.Message);
                        }
                    }
                });

                $modal.modal('hide');
            });


        });

        function download() {

            var date = new Date();
            var d = '' + date.getFullYear() + '_' + date.getMonth() + '_' + date.getDate() + '_' + date.getHours() + '_' +
                date.getMinutes() + '_' + date.getSeconds();

            var csv = FooTable.get('#example').toCSV(true);

            var lines = csv.split("\n");
            var out = "";

            $.each(lines, function(index, value) {

                val = value.split(",");
                out += val[0] + "," + val[1] + "\n";
            });
            var hiddenElement = document.createElement('a');
            hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(out);
            hiddenElement.target = '_blank';
            hiddenElement.download = 'Kiosk_Material_export_' + d + '.csv';
            hiddenElement.click();
            document.body.removeChild(hiddenElement);
        }
    </script>
@endsection
