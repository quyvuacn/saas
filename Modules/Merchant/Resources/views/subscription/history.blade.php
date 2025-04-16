@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Xem lịch sử thuê bao') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử thuê bao</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <input class="form-control col-2 float-left mr-2" id="from" placeholder="Từ ngày..." name="from" value="{{request('from')}}">
                            <input class="form-control col-2 float-left mr-2" id="to" placeholder="Đến ngày..." name="to" value="{{request('to')}}">
                            <button class="btn btn-primary p-2 history-search">Tra cứu</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti">
                            <thead>
                            <tr>
                                <th class="text-center">Mã giao dịch</th>
                                <th class="text-center">Tên máy / Mã máy</th>
                                <th class="text-center">Vị trí đặt máy</th>
                                <th class="text-center">Ngày hết hạn thuê bao trước gia hạn</th>
                                <th class="text-center">Số tiền gia hạn</th>
                                <th class="text-center">Ngày hết hạn thuê bao sau gia hạn</th>
                                <th class="text-center">Ngày tạo</th>
                                <th class="text-center">Tình trạng</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
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
                serverSide: true,
                ajax: "{{ route('merchant.subscription.history') }}",
                order: [[6, 'DESC']],
                columns: [
                    {data: 'code', name: 'code'},
                    {data: 'machine_model', name: 'machine_model'},
                    {data: 'machine_address', name: 'machine_address'},
                    {data: 'date_expiration_begin', name: 'date_expiration_begin',},
                    {data: 'request_price', name: 'request_price'},
                    {data: 'date_expiration_end', name: 'date_expiration_end'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'status', name: 'status', orderable: false, searchable: false},
                ]
            });

            flatpickr('#from', {
                locale: Vietnamese,
                dateFormat: "d/m/Y",
            });

            flatpickr('#to', {
                locale: Vietnamese,
                dateFormat: "d/m/Y",
            });

            $('.history-search').click(function () {
                var from = $('#from').val();
                var to = $('#to').val();
                table.destroy();
                table = $('#dataTable-vti').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('merchant.subscription.historyAjax') }}",
                        type: "POST",
                        data: ({
                            _token: "{{ csrf_token() }}",
                            from: from,
                            to: to,
                        }),
                    },
                    order: [[6, 'DESC']],
                    columns: [
                        {data: 'code', name: 'code'},
                        {data: 'machine_model', name: 'machine_model'},
                        {data: 'machine_address', name: 'machine_address'},
                        {data: 'date_expiration_begin', name: 'date_expiration_begin',},
                        {data: 'request_price', name: 'request_price'},
                        {data: 'date_expiration_end', name: 'date_expiration_end'},
                        {data: 'created_at', name: 'created_at'},
                        {data: 'status', name: 'status', orderable: false, searchable: false},
                    ]
                });
            });
        });
    </script>
@endsection
