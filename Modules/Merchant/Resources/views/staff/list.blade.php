@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Cài đặt tín dụng của khách') }}</h1>

    @include('merchant::layouts.partials.header-message')

    @include('merchant::layouts.partials.header-error')

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách nhân viên công ty</h6>
                    <p>Cài đặt danh sách khách hàng là nhân viên công ty, nhằm tự động cấp tín dụng cho tập người dùng xác định từ trước</p>
                    <p>
                        <strong>Lưu ý: Đây không phải là danh sách khách của bạn, khi khách hàng đăng ký có email trùng với email đã cài đặt, chương trình sẽ tự động xác nhận mức tín dụng cho khách</strong>
                    </p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <a href="javascript:void(0)" class="btn btn-danger" id="staff_bulk_delete" onclick="__bulkDeleteStaff()">Xóa nhiều</a>
                            <form action="{{ route('merchant.staff.import') }}" method="POST" enctype="multipart/form-data" style="display: inline-block" id="import-excel-form">
                                @csrf
                                <input type="file" id="excel-file" name="file" class="form-control" style="display: none">
                                <a href="javascript:void(0)" class="btn btn-primary" id="import-excel-btn">Nhập nhanh từ Excel</a>
                            </form>
                            <a href="{{ route('merchant.staff.export') }}" class="btn btn-default">Xuất ra file Excel</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th style="width: 10px">
                                    <div class="custom-control custom-checkbox small">
                                        <input type="checkbox" class="custom-control-input" name="staff_all" id="staff_all">
                                        <label class="custom-control-label" for="staff_all"></label>
                                    </div>
                                </th>
                                <th>Mã nhân viên</th>
                                <th>Email đăng ký</th>
                                <th style="vertical-align: middle">Tên đơn vị</th>
                                <th style="vertical-align: middle">Tín dụng được cấp</th>
                                @if(auth(MERCHANT)->user()->can('user.edit'))
                                    <th class="text-center" style="vertical-align: middle">Chức năng</th>
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
        <div class="modal fade" id="staffEdit" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <form id="staffEditForm">
                        <div class="modal-header">
                            <h5 class="modal-title">Sửa thông tin nhân viên</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" id="employee_id" name="employee_id" value="">
                            <div class="form-group">
                                <label for="employee_email">Email</label>
                                <input type="email" class="form-control" id="employee_email" name="employee_email" placeholder="Email nhân viên" required>
                            </div>
                            <div class="form-group">
                                <label for="employee_department">Đơn vị</label>
                                <input type="text" class="form-control" id="employee_department" name="employee_department" placeholder="Đơn vị" required>
                            </div>
                            <div class="form-group">
                                <label for="employee_quota">Tín dụng</label>
                                <input type="number" class="form-control" id="employee_quota" name="employee_quota" min="0" placeholder="Tín dụng" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info staff-edit-save-btn" onclick="__saveStaffInfo()">Lưu</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Hủy</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('extra-js')
    <script>
        // DELETE - ONE
        function __deleteStaff(id) {
            Swal.fire({
                title: 'Bạn muốn xóa Nhân viên này?',
                text: "Hành động này không thể khôi phục!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.value) {
                    axios.post('/staff/' + id + '/delete').then(response => {
                        if (response.status == 200 && response.data.status) {
                            Swal.fire(
                                response.data.message,
                                '',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Opps!',
                                response.data.message,
                                'error'
                            );
                        }
                        $('#dataTable-vti').DataTable().ajax.reload();
                    });
                }
            })
        }

        // DELETE - BULK
        function __bulkDeleteStaff(id) {
            var list_id = [];
            $(".staff_credit_select_class:checked").each(function () {
                list_id.push(this.value);
            });
            if (list_id.length > 0) {
                Swal.fire({
                    title: 'Bạn muốn xóa Những nhân viên này?',
                    text: "Hành động này không thể khôi phục!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy',
                }).then((result) => {
                    if (result.value) {
                        axios.post('/staff/bulk-delete', {list_id: list_id}).then(response => {
                            if (response.status == 200 && response.data.status) {
                                Swal.fire(
                                    response.data.message,
                                    '',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Opps!',
                                    response.data.message,
                                    'error'
                                );
                            }
                            $('#dataTable-vti').DataTable().ajax.reload();
                        });
                    }
                })
            } else {
                Swal.fire(
                    'Opps!',
                    'Không có dữ liệu để xóa',
                    'warning'
                );
            }
        }

        // EDIT EMPLOYEE
        function __saveStaffInfo() {
            var formData = new FormData($('#staffEditForm')[0]);
            var id = formData.get('employee_id');
            formData.delete('employee_id');
            axios.post('/staff/' + id + '/edit', formData).then(response => {
                if (response.status == 200 && response.data.status) {
                    Swal.fire(
                        response.data.message,
                        '',
                        'success'
                    );
                    $('#staffEdit').modal('hide');
                } else {
                    Swal.fire(
                        'Opps!',
                        response.data.message,
                        'error'
                    );
                }
                $('#dataTable-vti').DataTable().ajax.reload();
            });
        }

        $(function () {
            var table = $('#dataTable-vti').DataTable({
                processing: true,
                // serverSide: true,
                ajax: "{{ route('merchant.staff.list') }}",
                columns: [
                    {data: 'select', name: 'select', orderable: false, searchable: false},
                    {data: 'employee_code', name: 'employee_code'},
                    {data: 'employee_email', name: 'employee_email'},
                    {data: 'employee_department', name: 'employee_department'},
                    {data: 'credit_quota', name: 'employee_quota'},
                        @if(auth(MERCHANT)->user()->can('user.edit'))
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false
                    },
                    @endif
                ],
                order: [[2, 'ASC']],
            });

            // CHECK ALL
            $('body').delegate('#staff_all', 'click', function () {
                if ($(this).is(':checked')) {
                    $('.staff_credit_select_class').prop('checked', true);
                } else {
                    $('.staff_credit_select_class').prop('checked', false);
                }
            });

            // EDIT
            $('body').delegate('.staff-credit-edit-btn', 'click', function () {
                var staff_id = $(this).data('id');
                $('#employee_id').val(staff_id);
                $('#employee_email').val($(this).data('email'));
                $('#employee_quota').val($(this).data('quota'));
                $('#employee_department').val($(this).data('department'));
            });

            // IMPORT
            $('#import-excel-btn').click(function () {
                if ($(this).text() === 'Click để Import') {
                    $('#import-excel-form').submit();
                } else {
                    $("#excel-file").click();
                }
            });

            $("#excel-file").change(function (e) {
                var excel_file = e.target.files[0];
                if (excel_file) {
                    if (excel_file.size > 0 && excel_file.size <= 5000000 && (excel_file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')) {
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
                        if (excel_file.type !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                            alert('Không đúng định dạng Excel!');
                            return false;
                        }
                    }
                } else {
                    alert('Bạn chưa chọn file!');
                    $('#import-excel-btn').text('Nhập nhanh từ Excel')
                }
            });
        });
    </script>
@endsection
