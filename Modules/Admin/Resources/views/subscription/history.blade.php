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
                    <th>Máy bán hàng</th>
                    <th>Code</th>
                    <th>Ngày hết hạn trước khi duyệt</th>
                    <th>Ngày hết hạn sau khi duyệt</th>
                    <th>Ngày tạo</th>
                    <th>Trạng thái</th>
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

            ajax: "{{ route('admin.subscription.history', ['merchantId' => $merchantId]) }}",
            columns: [
                {data: 'machine_name', name: 'machine_name'},
                {data: 'code', name: 'code'},
                {data: 'date_expiration_begin', name: 'date_expiration_begin'},
                {data: 'date_expiration_end', name: 'date_expiration_end'},
                {data: 'created_at', name: 'created_at'},
                {data: 'status', name: 'status'},
            ],
            order: [4, 'DESC'],
        });
    });
</script>
@endsection
