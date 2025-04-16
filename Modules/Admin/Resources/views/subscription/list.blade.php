@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Danh sách các thuê bao') }}</h1>

    @include('admin::layouts.partials.header-message')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tất cả các thuê bao trên hệ thống</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Tên merchant</th>
                        <th>Máy bán hàng</th>
                        <th class="text-center">Ngày tạo</th>
                        <th class="text-center">Ngày hết hạn</th>
                        <th class="text-center">Tình trạng</th>
                        <th>Chức năng</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $(function () {
            var tables = $('#dataTable-vti').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.subscription.list') }}",
                columns: [
                    {data: 'merchant_info', name: 'merchant_info'},
                    {data: 'machine', name: 'machine'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'date_expire', name: 'date_expire'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [2, 'DESC'],
            });
        });
    </script>
@endsection
