@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị máy bán hàng của bạn') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách máy bán của bạn</h6>
                </div>
                <div class="card-body">
                    {{--        <td>--}}
                    {{--            2020/06/25--}}
                    {{--            <div class="small">--}}
                    {{--                <em>(Còn lại 15 ngày)</em>--}}
                    {{--            </div>--}}
                    {{--        </td>--}}
                    <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th style="vertical-align: middle; text-align: center;">Tên máy bán hàng/Model
                            </th>
                            <th style="vertical-align: middle; text-align: center;">Vị trí đặt máy
                            </th>
                            <th style="vertical-align: middle; text-align: center; width: 23%">Thông
                                số sản phẩm
                            </th>
                            <th style="width: 13%; vertical-align: middle; text-align: center;">Ngày
                                bắt đầu
                            </th>
                            <th style="width: 13%; vertical-align: middle; text-align: center;">Thời
                                hạn thuê bao
                            </th>
                            @if(auth(MERCHANT)->user()->can('machine.edit'))
                                <th style="vertical-align: middle; text-align: center; width: 15%">Chức
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

@endsection

@section('extra-js')
    <script>
        $(function () {
            var table = $('#dataTable-vti').DataTable({
                processing: true,
                // serverSide: true,
                ajax: "{{ route('merchant.machine.list') }}",
                columns: [
                    {data: 'model', name: 'model'},
                    {data: 'position', name: 'position'},
                    {data: 'spec', name: 'spec'}, // Wrong
                    {data: 'start_date', name: 'start_date'},
                    {data: 'expire_subscription', name: 'expire_subscription'},
                        @if(auth(MERCHANT)->user()->can('machine.edit'))
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false
                    },
                    @endif
                ],
                order: [[3, 'DESC']],
            });
            $('body').delegate('.stop-extend', 'click', function () {
                var request_content = '';
                var date_return_machine = '';
                Swal.fire({
                    icon: 'warning',
                    title: '{{__('Bạn muốn Ngừng thuê bao này?')}}',
                    html: '<div class="text-sm-left"><input type="date" name="date_return_machine" value="" required="required" class="form-control swal2-input date_return_machine" style="background: #fff!important" placeholder="Ngày trả máy"></div>',
                    input: 'textarea',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    inputValue: request_content,
                    inputPlaceholder: 'Nội dung yêu cầu',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: '{{__('Đồng ý')}}',
                    cancelButtonText: '{{__('Hủy')}}',
                    showLoaderOnConfirm: true,
                    preConfirm: (login) => {
                        request_content = $(Swal.getInput()).val();
                        var inputDate = $('input[name="date_return_machine"]');
                        date_return_machine = inputDate.val();
                        var mess1 = '';
                        var mess2 = '';
                        if (request_content.length <= 5) {
                            mess1 = '{{__(' Nội dung phải lớn hơn 5 ký tự!')}}';
                        }
                        if (!date_return_machine.length) {
                            mess2 += '{{__(' Ngày tháng là bắt buộc')}}';
                            inputDate.addClass('swal2-inputerror')
                        } else {
                            inputDate.removeClass('swal2-inputerror');
                        }
                        inputDate.change(function () {
                            inputDate.removeClass('swal2-inputerror');
                            Swal.showValidationMessage(mess1);
                        });
                        if (request_content.length <= 5 || !date_return_machine.length) {
                            Swal.showValidationMessage(mess1 + mess2);
                        }
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.value) {
                        var machine_id = $(this).data('id');

                        var data = {
                            request_content: request_content,
                            date_return_machine: date_return_machine
                        };
                        axios.post('/machine/' + machine_id + '/request-back', data).then(response => {
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
                });
                flatpickr('.date_return_machine', {
                    locale: Vietnamese,
                    dateFormat: "d/m/Y",
                    // minDate: "today"
                })
            });

            $('body').delegate('.change-machine-addr', 'click', function () {
                var machine_id = $(this).data('id');
                var machine_address = $(this).data('address');
                Swal.fire({
                    title: '{{__('Thay đổi chỗ đặt máy')}}',
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
                    inputValue: machine_address,
                    showCancelButton: true,
                    confirmButtonText: '{{__('Thay đổi')}}',
                    showLoaderOnConfirm: true,
                    preConfirm: (login) => {
                        machine_address = $(Swal.getInput()).val();
                        if (machine_address.length <= 5) {
                            Swal.showValidationMessage(
                                '{{__('Địa chỉ phải lớn hơn 5 ký tự!')}}'
                            )
                        }
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.value) {
                        axios.post('/machine/change-address/' + machine_id, {address: machine_address}).then(response => {
                            if (response.status == 200 && response.data.status) {
                                $('#dataTable-vti').DataTable().ajax.reload();
                                Swal.fire({
                                    position: 'center center',
                                    icon: 'success',
                                    title: response.data.message,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: response.data.message,
                                })
                            }
                        });
                    }
                })
            });
        });
    </script>
@endsection
