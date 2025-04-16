@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị công nợ của khách hàng') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách công nợ của khách</h6>
                    <p>Công nợ của khách sẽ được chốt từ ngày 1 đến 30 hàng tháng</p>
                    <strong>Kích hoạt chế độ thu hồi nợ để xem chi tiết</strong>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php
                        if (isset($isDebtLocked) && $isDebtLocked) {
                            $class = 'btn-success remove-collect-debt-btn';
                            $text  = 'Chế độ thu hồi nợ đã kích hoạt (click để hủy)';
                        } else {
                            $class = 'btn-primary collect-debt-btn';
                            $text  = 'Kích hoạt chế độ thu hồi nợ';
                        }
                        ?>
                        <div class="col-12 mb-2 debt-action">
                            <a href="javascript:void(0)" class="btn {{$class}}">{{$text}}</a>
                            @if(isset($isDebtLocked) && $isDebtLocked)
                                <a href="{{ route('merchant.user.exportDebt') }}" class="btn btn-default excel-export">Xuất ra file Excel</a>
                            @endif
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Email đăng ký</th>
                                <th style="vertical-align: middle">Mã nhân viên</th>
                                <th style="vertical-align: middle">Tên đơn vị</th>
                                <th style="vertical-align: middle">Tín dụng được cấp</th>
                                <th style="vertical-align: middle">Số nợ cần thu hồi</th>
                                @if(auth(MERCHANT)->user()->can('user.debt.edit'))
                                    <th class="text-center" style="vertical-align: middle">Duyệt</th>
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
        var table = null;

        function getUserDebt() {
            return $('#dataTable-vti').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('merchant.user.debt') }}",
                columns: [
                    {data: 'email', name: 'email'},
                    {data: 'code', name: 'code'},
                    {data: 'department', name: 'department'},
                    {data: 'quote', name: 'quote'},
                    {data: 'debt', name: 'debt'},
                        @if(auth(MERCHANT)->user()->can('user.debt.edit'))
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false
                    },
                    @endif
                ],
                order: [[0, 'ASC']],
            });
        }

        @if(isset($isDebtLocked) && $isDebtLocked)
            table = getUserDebt();
        @endif

        function createExcelBtn() {
            btn = $('<a />', {
                href: '{{ route('merchant.user.exportDebt') }}',
                text: 'Xuất ra file excel',
                class: 'btn btn-default excel-export',
            });
            return btn;
        }

        @if(auth(MERCHANT)->user()->can('user.debt.edit'))
            $(function () {
                // Single Receive Debt
                $('body').delegate('.user-debt-receive-btn', 'click', function () {
                    Swal.fire({
                        title: 'Bạn muốn Xác nhận thu hồi công nợ này?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Đồng ý',
                        cancelButtonText: 'Hủy',
                    }).then((result) => {
                        if (result.value) {
                            var request_id = $(this).data('id');
                            axios.post('/user/' + request_id + '/debt-received').then(response => {
                                if (response.status == 200 && response.data.status) {
                                    Swal.fire(
                                        response.data.message,
                                        '',
                                        'success'
                                    );
                                } else {
                                    Swal.fire(
                                        'Ops!',
                                        response.data.message,
                                        'error'
                                    );
                                }
                                $('#dataTable-vti').DataTable().ajax.reload();
                            });
                        }
                    })
                });

                // Collect Debt Active
                $('body').delegate('.collect-debt-btn', 'click', function () {
                    Swal.fire({
                        title: 'Bạn muốn Kích hoạt chế độ thu hồi công nợ?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Đồng ý',
                        cancelButtonText: 'Hủy',
                    }).then((result) => {
                        if (result.value) {
                            axios.post('/user/debt-collection-active').then(response => {
                                if (response.status == 200 && response.data.status) {
                                    Swal.fire(
                                        response.data.message,
                                        '',
                                        'success'
                                    );
                                    $(this).text('Chế độ thu hồi nợ đã kích hoạt (click để hủy)');
                                    $(this).removeClass('btn-primary');
                                    $(this).removeClass('collect-debt-btn');
                                    $(this).addClass('btn-success');
                                    $(this).addClass('remove-collect-debt-btn');
                                    table = getUserDebt();
                                    if ($('.debt-action').has('.excel-export').length <= 0) {
                                        $('.debt-action').append(createExcelBtn());
                                    }
                                } else {
                                    Swal.fire(
                                        'Ops!',
                                        response.data.message,
                                        'error'
                                    );
                                }
                            });
                        }
                    })
                });

                // Collect Debt Disable
                $('body').delegate('.remove-collect-debt-btn', 'click', function () {
                    Swal.fire({
                        title: 'Bạn muốn Hủy chế độ thu hồi công nợ?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Đồng ý',
                        cancelButtonText: 'Hủy',
                    }).then((result) => {
                        if (result.value) {
                            axios.post('{{route('merchant.user.debtCollectionDisable')}}').then(response => {
                                if (response.status == 200 && response.data.status) {
                                    Swal.fire(
                                        response.data.message,
                                        '',
                                        'success'
                                    );
                                    $(this).text('Kích hoạt chế độ thu hồi nợ');
                                    $(this).removeClass('btn-success');
                                    $(this).removeClass('remove-collect-debt-btn');
                                    $(this).addClass('btn-primary');
                                    $(this).addClass('collect-debt-btn');
                                    table.destroy();
                                    $('#dataTable-vti tbody').empty();
                                    $('.excel-export').remove();
                                } else {
                                    Swal.fire(
                                        'Ops!',
                                        response.data.message,
                                        'error'
                                    );
                                }
                            });
                        }
                    })
                });
            });
        @endif
    </script>
@endsection
