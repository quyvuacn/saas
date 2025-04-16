@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Xem lịch sử bán hàng') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row" id="vti-app">
        <div class="col-lg-12">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lịch sử bán hàng</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <input class="form-control col-2 float-left mr-2" id="code" placeholder="Nhập mã máy" name="code" value="{{request('code')}}">
                            <input class="form-control col-2 float-left mr-2" id="from" placeholder="Từ ngày..." name="from" value="{{request('from')}}">
                            <input class="form-control col-2 float-left mr-2" id="to" placeholder="Đến ngày..." name="to" value="{{request('to')}}">
                            <button class="btn btn-primary p-2 history-search">Tra cứu</button>
                            <a href="{{ route('merchant.machine.historiesExport') }}" class="float-lg-right p-2 btn btn-default btn-outline-secondary sale-history-export">Xuất file Excel Lịch sử bán hàng</a>
                        </div>
                    </div>
                    <table class="table table-bordered" id="dataTable-vti">
                        <thead>
                        <tr>
                            <th class="text-center">Mã giao dịch</th>
                            <th class="text-center">Mã máy / Tên máy </th>
                            <th class="text-center">Vị trí đặt máy</th>
                            <th class="text-center">Tên sản phẩm</th>
                            <th class="text-center">Giá bán</th>
                            <th class="text-center">Thời gian bán</th>
                            <th class="text-center">Người mua</th>
                            <th class="text-center">Tình trạng thanh toán</th>
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
                serverSide: true,
                ajax: "{{ route('merchant.machine.history') }}",
                order: [[5, 'DESC']],
                columns: [
                    {data: 'code', name: 'code'},
                    {data: 'machine_model', name: 'machine_model'},
                    {data: 'machine_address', name: 'machine_address'},
                    {data: 'product_name', name: 'product_name',},
                    {data: 'price', name: 'price'},
                    {data: 'sale_time', name: 'sale_time'},
                    {data: 'user', name: 'user'},
                    {data: 'status', name: 'status'},
                ],
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
                var code = $('#code').val();
                var from = $('#from').val();
                var to = $('#to').val();
                table.destroy();
                table = $('#dataTable-vti').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('merchant.machine.historyAjax') }}",
                        type: "POST",
                        data: ({
                            _token: "{{ csrf_token() }}",
                            code: code,
                            from: from,
                            to: to,
                        }),
                    },
                    columns: [
                        {data: 'code', name: 'code'},
                        {data: 'machine_model', name: 'machine_model'},
                        {data: 'machine_address', name: 'machine_address'},
                        {data: 'product_name', name: 'product_name',},
                        {data: 'price', name: 'price'},
                        {data: 'sale_time', name: 'sale_time'},
                        {data: 'user', name: 'user'},
                        {data: 'status', name: 'status'},
                    ],
                    order: [[5, 'DESC']]
                });
            });
        });
    </script>
@endsection
