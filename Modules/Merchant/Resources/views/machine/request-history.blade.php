@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Lịch sử yêu cầu') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row" id="vti-app">
        <div class="col-lg-12">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử yêu cầu</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th>Nội dung</th>
                            <th>Số lượng máy</th>
                            <th>Ngày yêu cầu</th>
                            <th>Ngày nhận máy</th>
                            <th style="max-width: 20%">Nơi giao máy</th>
                            <th style="max-width: 30%">Yêu cầu khác</th>
                            <th>Tình trạng</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('extra-js')
    <script>
        $(function () {
            var table = $('#dataTable-vti').DataTable({
                processing: true,
                // serverSide: true,
                ajax: "{{ route('merchant.machine.requestHistory') }}",
                order: [[2, 'DESC']],
                columns: [
                    {data: 'content', name: 'content'},
                    {data: 'machine_count', name: 'machine_count'},
                    {data: 'request_date', name: 'request_date'},
                    {data: 'request_receive', name: 'request_receive',},
                    {data: 'request_position', name: 'request_position', orderable: false},
                    {data: 'request_other', name: 'request_other', orderable: false},
                    {data: 'request_status', name: 'request_status', orderable: false},
                ],
            });
        });
    </script>
@endsection
