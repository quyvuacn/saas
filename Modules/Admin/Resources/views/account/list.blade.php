@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Danh sách tài khoản') }}</h1>

    @include('admin::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Các tài khoản quản trị hệ thống</h6>
                </div>
                <div class="card-body">
                    <div class="row d-flex mb-2 justify-content-between justify-content-start">
                        <div class="col-3 mt-1">
                            <a href="{{route('admin.account.create')}}" class="btn btn-primary">Thêm tài khoản mới</a>
                        </div>
                        <div class="input-group justify-content-end" style="width: calc(100% - 800px); min-width: 300px">
                            <select class="form-control" id="select-status">
                                <option value="-1">Chọn trạng thái</option>
                                <option value="0">Disable</option>
                                <option value="1">Enabled</option>
                            </select>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="search-status" style="cursor:pointer;">Tìm kiếm</span>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti">
                            <thead>
                            <tr>
                                <th>Tài khoản/Email</th>
                                <th>Các quyền được cấp</th>
                                <th>Ngày tạo</th>
                                <th>Đăng nhập lần cuối</th>
                                <th>Tình trạng</th>
                                <th class="text-center" style="width: 150px;">Chức năng</th>
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
                ajax: "{{ route('admin.account.list') }}",
                columns: [
                    {data: 'account', name: 'admin.name'},
                    {data: 'permission', name: 'permissions.permission_desc', orderable: false, searchable: false},
                    {data: 'date_create', name: 'admin.created_at'},
                    {data: 'last_login', name: 'last_login'},
                    {data: 'status', name: 'admin.status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [[2, 'DESC']],
            });
            $('body').delegate('.account-delete-btn', 'click', function () {
                Swal.fire({
                    title: 'Bạn muốn xóa Tài khoản này?',
                    text: "Hành động này không thể khôi phục!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy',
                }).then((result) => {
                    if (result.value) {
                        var user_id = $(this).data('id');
                        axios.post('/account/' + user_id + '/delete').then(response => {
                            if (response.status == 200 && response.data.status) {
                                Swal.fire(
                                    'Xóa Tài khoản thành công!',
                                    '',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Xóa khoản không thành công!',
                                    'Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn',
                                    'error'
                                );
                            }
                            $('#dataTable-vti').DataTable().ajax.reload();
                        });
                    }
                })
            });
            $('body').delegate('.account-active-btn', 'click', function () {
                Swal.fire({
                    title: 'Bạn muốn Active Tài khoản này?',
                    text: "",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Active',
                    cancelButtonText: 'Hủy',
                }).then((result) => {
                    if (result.value) {
                        var user_id = $(this).data('id');
                        axios.post('/account/' + user_id + '/toggle', {status:1}).then(response => {
                            if (response.status == 200 && response.data.status) {
                                Swal.fire(
                                    'Active Tài khoản thành công!',
                                    '',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Active Tài khoản không thành công!',
                                    'Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn',
                                    'error'
                                );
                            }
                            $('#dataTable-vti').DataTable().ajax.reload();
                        });
                    }
                })
            });
            $('body').delegate('.account-inactive-btn', 'click', function () {
                Swal.fire({
                    title: 'Bạn muốn Disable Tài khoản này?',
                    text: "",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Active',
                    cancelButtonText: 'Hủy',
                }).then((result) => {
                    if (result.value) {
                        var user_id = $(this).data('id');
                        axios.post('/account/' + user_id + '/toggle', {status:0}).then(response => {
                            if (response.status == 200 && response.data.status) {
                                Swal.fire(
                                    'Tài khoản đã tạm ngưng!',
                                    '',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Không thành công!',
                                    'Đã xảy ra lỗi hoặc Tài khoản không có quyền hạn',
                                    'error'
                                );
                            }
                            $('#dataTable-vti').DataTable().ajax.reload();
                        });
                    }
                })
            });

            function searchStatus(){
                var status = $('#select-status').find(':selected').val();
                table.destroy();
                table = $('#dataTable-vti').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.account.list') }}",
                        type: "GET",
                        data: ({
                            _token: "{{ csrf_token() }}",
                            status: status,
                        }),
                    },
                    columns: [
                        {data: 'account', name: 'admin.email'},
                        {data: 'permission', name: 'permissions.permission_desc'},
                        {data: 'date_create', name: 'admin.created_at'},
                        {data: 'last_login', name: 'last_login'},
                        {data: 'status', name: 'admin.status'},
                        {data: 'action', name: 'action', orderable: false, searchable: false},
                    ],
                    order: [[2, 'DESC']],
                });
            }

            $('#search-status').click(function () {
                searchStatus();
            });

        });
    </script>
@endsection


<style>
    .disabled{
        cursor: not-allowed;
        pointer-events: none;
    }
</style>
