@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị danh sách tài khoản phụ') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách tài khoản phụ</h6>
                </div>
                <div class="card-body">
                    @if(auth(MERCHANT)->user()->can('account.edit'))
                        <div class="row">
                            <div class="col-12 mb-2">
                                <a href="{{route('merchant.account.create')}}" class="btn btn-primary">Thêm tài khoản mới</a>
                            </div>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Tài khoản</th>
                                <th>Các quyền đã cấp</th>
                                <th style="width: 15%; vertical-align: middle">Ngày tạo/Cập nhật</th>
                                @if(auth(MERCHANT)->user()->can('account.edit'))
                                    <th class="text-center" style="width: 250px vertical-align: middle;">Chức
                                        năng
                                    </th>
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
            var tables = $('#dataTable-vti').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('merchant.account.list') }}",
                columns: [
                    {data: 'account', name: 'account'},
                    {data: 'permission', name: 'permission'},
                    {data: 'date', name: 'date'},
                    @if(auth(MERCHANT)->user()->can('account.edit'))
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                    @endif
                ],
                order: [[2, 'DESC']],
            });
            $('body').delegate('.account-delete-btn', 'click', function () {
                Swal.fire({
                    title: 'Bạn muốn xóa Merchant này?',
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
                        axios.post('/account/delete/' + user_id).then(response => {
                            console.log('response', response);
                            if (response.status == 200 && response.data.status) {
                                Swal.fire(
                                    response.data.message,
                                    '',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Xóa Tài khoản không thành công!',
                                    response.data.message,
                                    'error'
                                );
                            }
                            $('#dataTable-vti').DataTable().ajax.reload();
                        });
                    }
                })
            });
        });
    </script>
@endsection
