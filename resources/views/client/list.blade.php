@extends('layouts.app')

@section('content')
    <style>
        td {
            vertical-align: middle;
        }
    </style>

    <div class="container">
        <h2>Clients</h2>

        <!-- Button trigger modal -->
        <div class="m-3 justify-content-end d-flex">
            <button id="model-trigger" type="button" class="btn btn-secondary" data-type="create">Create</button>
        </div>

        <table id="clients_table" class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Client ID</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clients as $key => $value)
                    <tr>
                        <td>{{ __("client-name.$value->name") }}</td>
                        <td>{{ $value->id }}</td>
                        <td>{{ __("client-list.revoke.$value->revoked") }}</td>
                        <td>
                            <button type="button" class="btn btn-outline-secondary"
                                data-client="{{ $value->id }}">Detail</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <script>
        $(function() {
            let $dataTable = $("#clients_table").DataTable({
                "lengthMenu": [
                    [10, 25],
                    ['10 items', '25 items']
                ],
                "language": {
                    "search": "",
                    "infoEmpty": "0",
                    "infoFiltered": "",
                    "info": "_START_ - _TOTAL_",
                    "lengthMenu": "_MENU_ per page",
                    "paginate": {
                        "previous": "<",
                        "next": ">",
                        "first": "|<",
                        "last": ">|",
                    }
                }
            })

            // $("#model-trigger").click(function () {
            //     $("#create-form");
            // })

            // var myModal = new bootstrap.Modal(document.getElementById('myModal'));
        });
    </script>
@endsection
