@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Phân quyền tài khoản') }}</h1>

    @include('admin::layouts.partials.header-message')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quyền của tài khoản</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th style="width:220px">Tài khoản</th>
                        <th>Quản trị Tài khoản</th>
                        <th>Quản trị Merchant</th>
                        <th>Quản trị Máy bán hàng</th>
                        <th>Quản trị Request Máy bán hàng</th>
                        <th>Quản trị Thuê bao</th>
                        <th>Quản trị yêu cầu gia hạnThuê bao</th>
                        <th>Ngày tạo</th>
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
            var table = $('#dataTable-vti').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.account.permission') }}",
                columns: [
                    {data: 'account', name: 'admin.email'},
                    {data: 'management_account', name: 'permissions.permission_desc', orderable: false, searchable: false},
                    {data: 'management_merchant', name: 'permissions.permission_desc', orderable: false, searchable: false},
                    {data: 'management_machine', name: 'permissions.permission_desc', orderable: false, searchable: false},
                    {data: 'management_request_machine', name: 'permissions.permission_desc', orderable: false, searchable: false},
                    {data: 'management_subscription', name: 'permissions.permission_desc', orderable: false, searchable: false},
                    {data: 'management_subscription_request', name: 'permissions.permission_desc', orderable: false, searchable: false},
                    {data: 'created', name: 'admin.created_at'},
                ],
                order: [[7, 'DESC']],
            });
            function searchAccount(el){
                var email_name = $('#email-name').val();
                if (email_name.length < 3) {
                    alert('Độ dài từ khóa phải > 3 ký tự')
                }
                if (email_name.length > 255) {
                    alert('Độ dài từ khóa phải < 255 ký tự')
                }
                table.destroy();
                table = $('#dataTable-vti').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('admin.account.permissionAjax') }}",
                        type: "POST",
                        data: ({
                            _token: "{{ csrf_token() }}",
                            email_name: email_name,
                        }),
                    },
                    columns: [
                        {data: 'account', name: 'admin.email'},
                        {data: 'management_account', name: 'permissions.permission_desc', orderable: false, searchable: false},
                        {data: 'management_merchant', name: 'permissions.permission_desc', orderable: false, searchable: false},
                        {data: 'management_machine', name: 'permissions.permission_desc', orderable: false, searchable: false},
                        {data: 'management_request_machine', name: 'permissions.permission_desc', orderable: false, searchable: false},
                        {data: 'management_subscription', name: 'permissions.permission_desc', orderable: false, searchable: false},
                        {data: 'management_subscription_request', name: 'permissions.permission_desc', orderable: false, searchable: false},
                        {data: 'created', name: 'admin.created_at'},
                    ],
                    order: [[7, 'DESC']],
                });
            }

            $('body').delegate('.permission-change-btn', 'click', function () {
                var admin = $(this).data('admin');
                var permission = $(this).data('permission');
                var parent = $(this).parent();
                var check = $(parent).find('input').is(':checked') ? 0 : 1;
                axios.post('/account/permission/change/' + admin, {
                    admin: admin,
                    permission: permission,
                    check: check
                }).then(response => {
                    var mess = 'Thay đổi thất bại, hoặc không có quyền hạn!';
                    var icon = 'error';
                    if (response.status == 200 && response.data.status) {
                        mess = response.data.message;
                        icon = 'success';
                    }
                    Swal.fire({
                        position: 'top-end',
                        icon: icon,
                        height: 80,
                        title: mess,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#dataTable-vti').DataTable().ajax.reload();
                });
            });

            $("#email-name").on('keyup', function (e) {
                if (e.keyCode === 13) {
                    searchAccount();
                }
            });

            $('#account-search').click(function () {
                searchAccount();
            });
        });
    </script>
@endsection
