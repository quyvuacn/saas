@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị danh sách khách hàng') }}</h1>

    @include('merchant::layouts.partials.header-message')

    @include('merchant::layouts.partials.header-error')

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách khách mới đăng ký</h6>
                </div>
                <div class="card-body">
                    @if (auth(MERCHANT)->user()->can('user.edit'))
                        <div class="row mb-3">
                            <div class="col-6">
                                <a href="{{route('merchant.user.create')}}" class="btn btn-primary">
                                    Thêm người dùng mới
                                </a>

                                <form action="{{ route('merchant.user.import') }}" method="POST" enctype="multipart/form-data" style="display: inline-block" id="import-excel-form">
                                    @csrf
                                    <input type="file" id="excel-file" name="file" class="form-control" style="display: none" accept=".csv,application/vnd.ms-excel,.xlt,application/vnd.ms-excel,.xla,application/vnd.ms-excel,.xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,.xltx,application/vnd.openxmlformats-officedocument.spreadsheetml.template,.xlsm,application/vnd.ms-excel.sheet.macroEnabled.12,.xltm,application/vnd.ms-excel.template.macroEnabled.12,.xlam,application/vnd.ms-excel.addin.macroEnabled.12,.xlsb,application/vnd.ms-excel.sheet.binary.macroEnabled.12">
                                    <a href="javascript:void(0)" class="btn btn-primary" id="import-excel-btn">Nhập nhanh từ Excel</a>
                                </form>
                                <a href="{{ route('merchant.user.exportUser') }}" class="btn btn-default btn-outline-secondary excel-export">Mẫu file Excel</a>
                            </div>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Email đăng ký</th>
                                <th>Bộ phận</th>
                                <th>Số dư khả dụng</th>
                                <th>Điện thoại</th>
                                <th style="width: 15%; vertical-align: middle">Ngày đăng ký</th>
                                @if (auth(MERCHANT)->user()->can('user.credit.edit'))
                                    <th class="text-center" style="width: 20%; vertical-align: middle">Duyệt</th>
                                @endif
                                @if (auth(MERCHANT)->user()->can('user.edit'))
                                    <th class="text-center" style="width: 15%; vertical-align: middle;">Xóa yêu cầu</th>
                                @endif
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
                // serverSide: true,
                ajax: "{{ route('merchant.user.list') }}",
                columns: [
                    {data: 'email', name: 'email'},
                    {data: 'department', name: 'department'},
                    {data: 'coin', name: 'coin'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'date', name: 'date'},
                        @if (auth(MERCHANT)->user()->can('user.credit.edit'))
                    {
                        data: 'approve', name: 'approve', orderable: false, searchable: false
                    },
                        @endif
                        @if (auth(MERCHANT)->user()->can('user.edit'))
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false
                    },
                    @endif
                ],
                order: [[4, 'DESC']],
            });
            @if (auth(MERCHANT)->user()->can('user.edit'))
                $('body').delegate('.user-delete-btn', 'click', function () {
                    Swal.fire({
                        title: 'Bạn muốn xóa User này?',
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
                            axios.post('/user/delete/' + user_id).then(response => {
                                if (response.status == 200 && response.data.status) {
                                    Swal.fire(
                                        response.data.message,
                                        '',
                                        'success'
                                    );
                                } else {
                                    Swal.fire(
                                        'Xóa User không thành công!',
                                        response.data.message,
                                        'error'
                                    );
                                }
                                $('#dataTable-vti').DataTable().ajax.reload();
                            });
                        }
                    })
                });

                $("#excel-file").change(function (e) {
                    var excel_file = e.target.files[0];
                    var file_type_include = [
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
                        'application/vnd.ms-excel.sheet.macroEnabled.12',
                        'application/vnd.ms-excel.template.macroEnabled.12',
                        'application/vnd.ms-excel.addin.macroEnabled.12',
                        'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
                    ]
                    if (excel_file) {
                        console.log('excel_file', excel_file);
                        console.log('excel_file.type', excel_file.type);
                        if (excel_file.size > 0 && excel_file.size <= 5000000 && (file_type_include.includes(excel_file.type))) {
                            $('#import-excel-btn').text('Click để Import')
                        } else {
                            $(this).val('');
                            $('#import-excel-btn').text('Nhập nhanh từ Excel');
                            if (excel_file.size > 5000000) {
                                alert('Dung lượng file vượt quá 5Mb!');
                                return false;
                            }
                            if (excel_file.size <= 0) {
                                alert('Không được Import file rỗng!');
                            }
                            if (!file_type_include.includes(excel_file.type)) {
                                console.log('excel_file.type_alert', excel_file.type);
                                alert('Không đúng định dạng Excel!');
                                return false;
                            }
                        }
                    } else {
                        alert('Bạn chưa chọn file!');
                        $('#import-excel-btn').text('Nhập nhanh từ Excel')
                    }
                });

                $('#import-excel-btn').click(function () {
                    if ($(this).text() === 'Click để Import') {
                        $('#import-excel-form').submit();
                    } else {
                        $("#excel-file").click();
                    }
                });
            @endif
        });
    </script>
@endsection
