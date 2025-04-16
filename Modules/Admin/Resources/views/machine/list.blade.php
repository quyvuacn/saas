@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Danh sách các máy bán hàng') }}</h1>

    @include('admin::layouts.partials.header-message')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Các máy bán hàng của VTI</h6>
        </div>
        <div class="card-body">
            <div class="row pb-3">
                <div class="col-6">
                    <a href="{{route('admin.machine.create')}}"><button class="btn btn-primary">Thêm máy mới</button></a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Tên máy<br>Model</th>
                        <th>Merchant quản lý</th>
                        <th>Device ID</th>
                        <th style="min-width: 200px">Cấu hình máy bán hàng</th>
                        <th class="text-center">Ngày nhập</th>
                        <th>Thông tin khác</th>
                        <th>Tình trạng</th>
                        <th style="min-width: 100px">Chức năng</th>
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
                // serverSide: true,
                ajax: "{{ route('admin.machine.list', ['merchant_id' => $merchant]) }}",
                order: [4, 'DESC'],
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'merchant_name', name: 'merchant_name'},
                    {data: 'device_id', name: 'device_id'},
                    {data: 'machine_system_info', name: 'machine_system_info', orderable: false, searchable: false},
                    {data: 'date_added', name: 'date_added'},
                    {data: 'machine_note', name: 'machine_note', orderable: false, searchable: false},
                    {data: 'status', name: 'status', searchable: false},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ]
            });
        });

        function deleteMachine(response){
            if(response.data.status == 1){
                showMessageSuccess();
                $('#dataTable-vti').DataTable().ajax.reload();
            } else {
                var msg = (typeof response.data.msg !== undefined) ? response.data.msg : '';
                showMessageError(msg);
            }
        }
        $('body').delegate('.change-machine-device', 'click', function () {
            var machine_id = $(this).data('id');
            var machine_device_id = $(this).data('deviceid');
            Swal.fire({
                title: '{{__('Thay đổi Device ID cuả máy')}}',
                input: 'text',
                inputAttributes: {
                    autocapitalize: 'off'
                },
                inputValue: machine_device_id,
                showCancelButton: true,
                confirmButtonText: '{{__('Thay đổi')}}',
                showLoaderOnConfirm: true,
                preConfirm: (login) => {
                    machine_device_id = $(Swal.getInput()).val();
                    if (machine_device_id.length <= 5) {
                        Swal.showValidationMessage(
                            '{{__('Device ID phải lớn hơn 5 ký tự!')}}'
                        )
                    }
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value) {
                    axios.post('/machine/change-device/' + machine_id, {device_id: machine_device_id}).then(response => {
                        if (response.status == 200) {
                            if(response.data.status){
                                $('#dataTable-vti').DataTable().ajax.reload();
                                showMessageSuccess(response.data.message)
                            } else {
                                showMessageError(response.data.message)
                            }
                        } else {
                            showMessageError();
                        }
                    });
                }
            })
        });
    </script>
@endsection
