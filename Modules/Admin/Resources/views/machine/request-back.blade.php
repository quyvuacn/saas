@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Yêu cầu trả máy bán hàng') }}</h1>

    @include('admin::layouts.partials.header-message')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Các yêu cầu trả lại máy bán hàng</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable-vti">
                    <thead>
                    <tr>
                        <th>Tên máy</th>
                        <th>Model máy</th>
                        <th>Địa chỉ nhận máy</th>
                        <th>Ngày tạo yêu cầu</th>
                        <th>Merchant yêu cầu</th>
                        <th>Ngày trả máy</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Chức năng</th>
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
                ajax: "{{ route('admin.machine.requestBack') }}",
                columns: [
                    {data: 'machine_name', name: 'machine_name'},
                    {data: 'machine_model', name: 'machine_model'},
                    {data: 'address', name: 'address'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'merchant', name: 'merchant',},
                    {data: 'date_receive', name: 'date_receive'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [3, 'DESC'],
            });
        });

        function finalApproveRequestBackMachine(machineId) {
            Swal.fire({
                title: '',
                text: "Bạn có chắc đã hoàn tất thu hồi máy",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xác nhận'
            }).then((result) => {
                if (result.value) {
                    var url = '/machine/final-approve-request-back/' + machineId;
                    axios.post(url).then(response => {
                        if(response.data.status == 1) {
                            showMessageSuccess('Thu hồi máy thành công');
                            $('#dataTable-vti').DataTable().ajax.reload();
                        } else {
                            showMessageError();
                        }
                    }).catch(function (error) {
                        showMessageError();
                    });
                }
            })
        }

        function cancelRequestBackMachine(machineId) {

            Swal.fire({
                title: '',
                text: "Bạn có chắc muốn hủy yêu cầu trả máy",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xác nhận'
            }).then((result) => {
                if (result.value) {
                    var url = '/machine/cancel-request-back/' + machineId;
                    axios.post(url).then(response => {
                        if(response.data.status == 1) {
                            showMessageSuccess(response.data.message);
                            $('#dataTable-vti').DataTable().ajax.reload();
                        } else {
                            showMessageError(response.data.message);
                        }
                    }).catch(function (error) {
                        showMessageError();
                    });
                }
            })
        }
    </script>
@endsection
