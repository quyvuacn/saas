@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Phân quyền tài khoản phụ') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quyền của tài khoản</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 my-3">
                    <div class="input-group">
                        <input type="text" class="form-control" id="email-name" placeholder="Nhập tài khoản cần phân quyền" aria-describedby="" required>
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="account-search" style="cursor:pointer;">Tìm kiếm tài khoản</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Tài khoản</th>
                        <th>Quản trị Khách hàng</th>
                        <th>Quản trị Tài khoản</th>
                        <th>Quản trị Máy bán hàng</th>
                        <th>Quản trị Bán hàng</th>
                        <th>Ngày tạo</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Yêu cầu xóa Merchant không
                                    thành công!</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <h5 class="alert alert-danger text-center">
                                    Bạn không được phép xóa Merchant đang có máy bán hàng hoạt động.
                                </h5>
                                <p>
                                    Vui lòng hoàn thiện <strong>thủ tục thu hồi máy bán hàng</strong> của
                                    Merchant trước khi xóa tài
                                    khoản Merchant.
                                </p>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <a href="">
                                            <em class="fa fa-sm fa-chevron-right"></em>
                                            Xem thông tin thuê bao của Merchant
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="">
                                            <em class="fa fa-sm fa-chevron-right"></em>
                                            Xem danh sách các máy bán hàng của Merchant
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel1">Bạn có chắc chắn muốn xóa
                                    Merchant hay không?</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    Hãy đảm bảo rằng hợp đồng đã ký với Merchant đã được thanh lý trước khi
                                    xóa tài khoản của Merchant.
                                </p>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                    <label class="form-check-label" for="defaultCheck1">
                                        Các nghĩa vụ trong hợp đồng với Merchant đã được thực hiện hết
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck2">
                                    <label class="form-check-label" for="defaultCheck2">
                                        Đã ký thanh lý hợp đồng (hoặc tự động thanh lý hợp đồng)
                                    </label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger">
                                    Xác nhận xóa Merchant
                                </button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                    Đóng
                                </button>
                            </div>
                        </div>
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
                // serverSide: true,
                ajax: "{{ route('merchant.account.permission') }}",
                columns: [
                    {data: 'account', name: 'account'},
                    {data: 'management_customer', name: 'management_customer', orderable: false, searchable: false},
                    {data: 'management_account', name: 'management_account', orderable: false, searchable: false},
                    {data: 'management_machine', name: 'management_machine', orderable: false, searchable: false},
                    {data: 'management_selling', name: 'management_selling', orderable: false, searchable: false},
                    {data: 'created', name: 'created'},
                ],
                order: [[5, 'DESC']],
            });
            $('body').delegate('.permission-change-btn', 'click', function () {
                var merchant = $(this).data('merchant');
                var permission = $(this).data('permission');
                var parent = $(this).parent();
                var check = $(parent).find('input').is(':checked') ? 0 : 1;
                axios.post('/account/permission/change/' + merchant, {
                    merchant: merchant,
                    permission: permission,
                    check: check
                }).then(response => {
                    if (response.status == 200 && response.data.status) {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            height: 80,
                            title: response.data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    } else {
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            height: 80,
                            title: response.data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                    $('#dataTable-vti').DataTable().ajax.reload();
                });
            });

            function searchAccount(){
                var email_name = $('#email-name').val();
                if (email_name.length < 3) {
                    alert('Độ dài từ khóa phải > 3 ký tự');
                    return false;
                }
                if (email_name.length > 255) {
                    alert('Độ dài từ khóa phải < 255 ký tự');
                    return false;
                }
                table.destroy();
                table = $('#dataTable-vti').DataTable({
                    processing: true,
                    // serverSide: true,
                    ajax: {
                        url: "{{ route('merchant.account.permissionAjax') }}",
                        type: "POST",
                        data: ({
                            _token: "{{ csrf_token() }}",
                            email_name: email_name,
                        }),
                    },
                    columns: [
                        {data: 'account', name: 'account'},
                        {data: 'management_customer', name: 'management_customer', orderable: false, searchable: false},
                        {data: 'management_account', name: 'management_account', orderable: false, searchable: false},
                        {data: 'management_machine', name: 'management_machine', orderable: false, searchable: false},
                        {data: 'management_selling', name: 'management_selling', orderable: false, searchable: false},
                        {data: 'created', name: 'created'},
                    ],
                    order: [[5, 'DESC']],
                });
            }

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

