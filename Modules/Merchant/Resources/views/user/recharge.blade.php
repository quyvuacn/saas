@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị yêu cầu nạp tiền') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách khách gửi yêu cầu nạp coin</h6>
                </div>
                <div class="card-body">
                    @if(auth(MERCHANT)->user()->can('user.coin.request.edit'))
                        <div class="row">
                            <div class="col-12 mb-2">
                                <a href="{{route('merchant.user.rechargeCreate')}}" class="btn btn-primary">Tạo yêu cầu nạp coin cho khách</a>
                                <a href="{{ route('merchant.user.rechargeExport') }}" class="btn btn-default btn-outline-secondary recharge-export">Xuất file Excel lịch sử nạp tiền</a>
                            </div>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Mã giao dịch</th>
                                <th>Email người dùng</th>
                                <th class="text-center">Ngày tạo yêu cầu</th>
                                <th class="text-center">Coin cần nạp</th>
                                <th class="text-center">Tiền thanh toán</th>
                                <th class="text-center">Trạng thái</th>
                                @if(auth(MERCHANT)->user()->can('user.coin.request.edit'))
                                    <th class="text-center">Duyệt</th>
                                    <th class="text-center">Duyệt nhanh</th>
                                    <th class="text-center">Xóa yêu cầu</th>
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
                ajax: "{{ route('merchant.user.recharge') }}",
                columns: [
                    {data: 'transaction', name: 'transaction'},
                    {data: 'email', name: 'email'},
                    {data: 'date', name: 'date'},
                    {data: 'coin', name: 'coin'},
                    {data: 'money', name: 'money'},
                    {data: 'status', name: 'status'},
                    @if(auth(MERCHANT)->user()->can('user.coin.request.edit'))
                    {
                        data: 'optional_approve', name: 'optional_approve', orderable: false, searchable: false
                    },
                    {data: 'quick_approve', name: 'quick_approve', orderable: false, searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                    @endif
                ],
                order: [[2, 'DESC']],
            });
            @if(auth(MERCHANT)->user()->can('user.coin.request.edit'))
                // APPROVE ALL
                // $('body').delegate('.approve-all-btn', 'click', function () {
                //     Swal.fire({
                //         title: 'Bạn muốn duyệt tất cả Yêu cầu này này?',
                //         icon: 'warning',
                //         showCancelButton: true,
                //         confirmButtonColor: '#3085d6',
                //         cancelButtonColor: '#d33',
                //         confirmButtonText: 'Đồng ý',
                //         cancelButtonText: 'Hủy',
                //     }).then((result) => {
                //         if (result.value) {
                //             axios.post('/user/approve-all').then(response => {
                //                 if (response.status == 200 && response.data.status) {
                //                     Swal.fire(
                //                         response.data.message,
                //                         '',
                //                         'success'
                //                     );
                //                 } else {
                //                     Swal.fire(
                //                         'Ops!',
                //                         response.data.message,
                //                         'error'
                //                     );
                //                 }
                //                 $('#dataTable-vti').DataTable().ajax.reload();
                //             });
                //         }
                //     })
                // });
                // DELETE
                $('body').delegate('.request-delete-btn', 'click', function () {
                    Swal.fire({
                        title: 'Bạn muốn xóa Request này?',
                        text: "Hành động này không thể khôi phục!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Xóa',
                        cancelButtonText: 'Hủy',
                    }).then((result) => {
                        if (result.value) {
                            var request_id = $(this).data('id');
                            axios.post('/user/recharge-delete/' + request_id).then(response => {
                                if (response.status == 200 && response.data.status) {
                                    Swal.fire(
                                        response.data.message,
                                        '',
                                        'success'
                                    );
                                } else {
                                    Swal.fire(
                                        'Xóa Yêu cầu không thành công!',
                                        response.data.message,
                                        'error'
                                    );
                                }
                                $('#dataTable-vti').DataTable().ajax.reload();
                            });
                        }
                    })
                });
                // Quick approve
                $('body').delegate('.request-quick-approve-btn', 'click', function () {
                    Swal.fire({
                        title: 'Bạn muốn Duyệt yêu cầu này?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Duyệt',
                        cancelButtonText: 'Hủy',
                    }).then((result) => {
                        if (result.value) {
                            var request_id = $(this).data('id');
                            axios.post('/user/recharge-quick-approve/' + request_id).then(response => {
                                if (response.status == 200 && response.data.status) {
                                    Swal.fire(
                                        response.data.message,
                                        '',
                                        'success'
                                    );
                                } else {
                                    Swal.fire(
                                        'Duyệt Yêu cầu không thành công!',
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
