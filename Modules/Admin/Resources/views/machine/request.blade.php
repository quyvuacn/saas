@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Danh sách các yêu cầu máy bán hàng') }}</h1>

    @include('admin::layouts.partials.header-message')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Các yêu cầu máy bán hàng mới</h6>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Nội dung yêu cầu</th>
                        <th class="text-center">Ngày tạo yêu cầu</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-center">
                            Ngày yêu cầu<br>
                            Ngày nhận máy
                        </th>
                        <th>Merchant yêu cầu</th>
                        <th class="text-center">Tình trạng</th>
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
                ajax: "{{ route('admin.machine.request') }}",
                columns: [
                    {data: 'title', name: 'title'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'machine_request_count', name: 'machine_request_count'},
                    {data: 'time_request', name: 'time_request', orderable: false, searchable: false},
                    {data: 'merchant_id', name: 'merchant_id',},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [1, 'DESC'],
            });
        });
        function approveRequest(id) {
            Swal.fire({
                title: '',
                text: "Bạn có chắc đã hoàn tất lắp đặt máy",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xác nhận'
            }).then((result) => {
                if (result.value) {
                    var url = '/machine/final-approve-request/' + id;
                    axios.post(url).then(response => {
                        if(response.data.status == 1) {
                            showMessageSuccess('Thay đổi trạng thái thành công');
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
    </script>

@endsection


