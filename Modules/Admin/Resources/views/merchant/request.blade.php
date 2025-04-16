@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Danh sách các yêu cầu tạo tài khoản merchant') }}</h1>

    @include('admin::layouts.partials.header-message')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Yêu cầu tạo tài khoản</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Tên tài khoản</th>
                        <th>Tên công ty/cá nhân</th>
                        <th>Email liên hệ</th>
                        <th>Số điện thoại</th>
                        <th>Ngày tạo yêu cầu</th>
                        <th>Tình trạng</th>
                        <th class="text-center">Chức năng</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($merchants as $merchant)
                        <tr>
                            <td><span class="text-primary">{{$merchant->account}}</span></td>
                            <td>@if(!empty($merchant->merchantInfo)) {{$merchant->merchantInfo->merchant_company}} / {{$merchant->merchantInfo->merchant_name}} @endif</td>
                            <td>{{$merchant->email}}</td>
                            <td>{{$merchant->phone}}</td>
                            <td data-sort="{{strtotime($merchant->created_at)}}">{{date('d/m/Y', strtotime($merchant->created_at))}}</td>
                            <td class="text-center">
                                @switch($merchant->status)
                                    @case($merchant::REQUEST_WAITING)
                                    <label class="badge badge-dark">Chờ ký hợp đồng</label>
                                    @break
                                    @case($merchant::REQUEST_WAITING_SETUP)
                                    <label class="badge badge-warning">Đang chuyển máy</label>
                                    @break
                                    @case($merchant::REQUEST_CANCEL)
                                    <label class="badge badge-danger">Đã hủy</label>
                                    @break
                                    @case($merchant::REQUEST_SUCCESS)
                                    <label class="badge badge-success">Đã duyệt</label>
                                    @break
                                    @default
                                    <label class="badge badge-primary">Yêu cầu mới</label>
                                    @break
                                @endswitch
                            </td>
                            <td class="text-center">
                                @if($merchant->status == $merchant::REQUEST_WAITING_SETUP)
                                    <a href="javascript:;" onclick="approveRequest({{$merchant->id}})" class="btn btn-success mb-2">
                                        Hoàn tất
                                    </a>
                                @elseif ($merchant->status == $merchant::REQUEST_CANCEL || $merchant->status == $merchant::REQUEST_SUCCESS)
                                @else
                                    <a href="{{route('admin.merchant.requestDetail', ['merchantRequest' => $merchant->id])}}" class="btn btn-primary mb-2">
                                        Duyệt
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $(document).ready(function() {
            $('#dataTable1').DataTable({
                "order": [4, "DESC" ]
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
                    axios.post('/merchant/final-approve-request/' + id).then(response => {
                        if (response.data.status == 1) {
                            showMessageSuccess('Thay đổi trạng thái thành công')
                            setTimeout(function () {
                                location.reload();
                            }, 1500)
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
