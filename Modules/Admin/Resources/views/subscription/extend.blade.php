@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Danh sách yêu cầu gia hạn thuê bao') }}</h1>

    @include('admin::layouts.partials.header-message')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Các yêu cầu gia hạn</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3">
                Trường hợp khách hàng nạp tiền tại văn phòng hoặc khách hàng chuyển khoản và nhờ VTI gia
                hạn hợp đồng, thì quản trị viên dùng chức năng
                <a href="{{route('admin.subscription.create')}}" class="btn btn-sm btn-primary">Thêm yêu cầu mới</a>
                để hỗ trợ khách gia hạn
            </div>
            <div class="row mt-4">
                <div class="col-6 mb-2">
                    <a href="{{route('admin.subscription.create')}}" class="btn btn-primary">Thêm yêu cầu mới</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable-vti">
                    <thead>
                    <tr>
                        <th>Tên merchant</th>
                        <th>Tên máy / Model</th>
                        <th>Yêu cầu</th>
                        <th class="text-center">Ngày tạo</th>
                        <th>Số tiền thanh toán</th>
                        <th>Tình trạng</th>
                        <th style="width: 110px">Chức năng</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Bạn có chắc chắn đã hoàn tất gia hạn thuê bao?</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" onclick="sendRequestSubscription(this, {{$statusRequestSuccess}})" class="btn btn-send-request btn-success btn-confirm-success">
                        Xác nhận hoàn tất
                    </button>
                    <button type="button" onclick="sendRequestSubscription(this, {{$statusRequestNew}})" class="btn btn-send-request btn-dark btn-reset-request">
                        Reset yêu cầu
                    </button>
                    <button type="button" onclick="sendRequestSubscription(this, {{$statusRequestCancel}})" class="btn btn-send-request btn-danger btn-cancel-request">
                        Hủy yêu cầu
                    </button>
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
                ajax: "{{ route('admin.subscription.extend') }}",
                columns: [
                    {data: 'merchant', name: 'merchant'},
                    {data: 'machine', name: 'machine'},
                    {data: 'request_content', name: 'request_content'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'price', name: 'price'},
                    {data: 'status', name: 'status'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                order: [3, 'desc']
            });
        });
        function approveSubscription(id) {
            $('.btn-send-request').data('id', id);
            showModalSubscription();
        }
        function sendRequestSubscription(e, status) {
            closeModalSubscription();
            var id = $(e).data('id');
            loading();
            axios.post('/subscription/finalApproveRequest/' + id, {status:status}).then(response => {
                hideLoading();
                if (response.status == 200 && response.data.status == 1) {
                    showMessageSuccess('Thay đổi trạng thái thành công')
                } else {
                    showMessageError();
                }
                $('#dataTable-vti').DataTable().ajax.reload();
            }).catch(function (error) {
                hideLoading();
                showMessageError();
            });
        }
        function showModalSubscription() {
            $('#exampleModal').modal('show');
        }
        function closeModalSubscription() {
            $('#exampleModal').modal('hide');
        }
    </script>
@endsection
