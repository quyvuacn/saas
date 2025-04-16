@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị tín dụng của khách hàng') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách tín dụng đã cấp</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Email đăng ký</th>
                                <th style="width: 15%; vertical-align: middle">Ngày đăng ký</th>
                                <th style="width: 15%; vertical-align: middle">Số dư khả dụng</th>
                                <th style="width: 15%; vertical-align: middle">Hạn mức hiện tại</th>
                                <th style="width: 15%; vertical-align: middle">Số điện thoại</th>
                                @if (auth(MERCHANT)->user()->can('user.credit.edit'))
                                    <th class="text-center" style="width: 20%; vertical-align: middle">Duyệt</th>
                                    <th class="text-center" style="width: 15%; vertical-align: middle;">Xóa tín dụng</th>
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
                serverSide: true,
                ajax: "{{ route('merchant.user.credit') }}",
                columns: [
                    {data: 'email', name: 'email'},
                    {data: 'date', name: 'date'},
                    {data: 'coin', name: 'coin'},
                    {data: 'quota', name: 'quota'},
                    {data: 'phone_number', name: 'phone_number'},
                        @if (auth(MERCHANT)->user()->can('user.credit.edit'))
                    {
                        data: 'approve', name: 'approve', orderable: false, searchable: false
                    },
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                    @endif
                ],
                order: [[1, 'DESC']],
            });
            @if (auth(MERCHANT)->user()->can('user.credit.edit'))
                $('body').delegate('.user-credit-delete-btn', 'click', function () {
                Swal.fire({
                    title: 'Bạn muốn xóa Tín dụng của User này?',
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
                        axios.post('/user/credit-delete/' + user_id).then(response => {
                            if (response.status == 200 && response.data.status) {
                                Swal.fire(
                                    response.data.message,
                                    '',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Xóa Tín dụng không thành công!',
                                    response.data.message,
                                    'error'
                                );
                            }
                            $('#dataTable-vti').DataTable().ajax.reload();
                        });
                    }
                })
            });
            @endif
        });
    </script>
@endsection
