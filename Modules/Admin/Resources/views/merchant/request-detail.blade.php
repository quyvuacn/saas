@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Duyệt yêu cầu đăng ký Merchant') }}</h1>

    @if ($errors->any())
        <div class="alert alert-danger border-left-danger alert-dismissible fade show" role="alert">
            <ul class="pl-4 my-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Nội dung yêu cầu</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Tài khoản đăng nhập: <strong>{{$merchantRequest->account}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Tên
                            hiển thị: <strong>{{$merchantRequest->name}}</strong></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Số
                            điện thoại liên hệ: <a href="tel:{{$merchantRequest->phone}}" class="font-weight-bold">{{$merchantRequest->phone}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Email
                            liên hệ:
                            <a class="font-weight-bold" href="mailto:{{$merchantRequest->email}}">{{$merchantRequest->email}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span style="width: 200px">Địa
                                chỉ:</span> <strong class="text-right">@if (!empty($merchantRequest->merchantInfo)) {{$merchantRequest->merchantInfo->merchant_address}} @endif</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Ngày
                            tạo yêu cầu: <strong>{{date('H:i d/m/Y', strtotime($merchantRequest->created_at))}}</strong></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Tình
                            trạng yêu cầu:
                            @if($merchantRequest->status == $merchantRequest::REQUEST_NEW)
                            <label class="badge badge-primary">
                                Mới yêu cầu
                            </label>
                            @else
                                <label class="badge badge-dark">
                                    Chờ ký hợp đồng
                                </label>
                            @endif
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Duyệt yêu cầu</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{route('admin.merchant.approveRequest', ['merchantRequest' => $merchantRequest->id])}}">
                        @csrf
                        <div class="alert alert-info">
                            Hãy gọi điện cho merchant để thẩm định lại yêu cầu, và xin thêm các yêu cầu
                            khác.
                        </div>
                        <h5>Điền các thông tin liên quan như sau:</h5>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="merchant_audit" id="exampleRadios1"  value="1" @if(old('merchant_audit', 1) == 1) checked="checked" @endif>
                            <label class="form-check-label" for="exampleRadios1">
                                @if($merchantRequest->status == $merchantRequest::REQUEST_WAITING)
                                    Ký hợp đồng thành công.
                                @else
                                    Thẩm định thành công, ký kết hợp đồng.
                                @endif
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="merchant_audit" id="exampleRadios2"  value="2" @if(old('merchant_audit', 1) == 2) checked="checked" @endif>
                            <label class="form-check-label" for="exampleRadios2">
                                @if($merchantRequest->status == $merchantRequest::REQUEST_WAITING)
                                    Ký hợp đồng không thành công, hủy yêu cầu.
                                @else
                                    Thẩm định không thành công, hủy yêu cầu.
                                @endif
                            </label>
                        </div>
                        <div class="form-group mt-4 box-option option-1">
                            <label for="machine_count">Số máy bán hàng cần cung cấp</label>
                            <input type="number" class="form-control" name="machine_count" id="machine_count" placeholder="Nhập số lượng máy sẽ cung cấp" value="{{old('machine_count', $merchantRequest->machine_count)}}">
                        </div>
                        <div class="form-group mt-4 box-option option-1">
                            <label for="merchant_active_date">Ngày bắt đầu thuê bao</label>
                            <input type="date" class="form-control" name="merchant_active_date" id="merchant_active_date" placeholder="DD/MM/YYYY" value="{{old('merchant_active_date', $dateActive)}}">
                        </div>
                        <div class="form-group mt-4 option-1 box-option">
                            <label for="merchant_other_request">Các yêu cầu khác</label>
                            <textarea class="form-control" name="merchant_other_request" id="merchant_other_request" rows="3">{{old('merchant_other_request', $merchantRequest->merchantInfo->merchant_other_request)}}</textarea>
                        </div>
                        <div class="form-group mt-4 box-option option-2">
                            <label for="merchant_cancel_reason">Lý do</label>
                            <textarea class="form-control" id="merchant_cancel_reason" name="merchant_cancel_reason" rows="3">{{old('merchant_cancel_reason', $merchantRequest->merchantInfo->merchant_cancel_reason)}}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-user btn-block">
                            Cập nhật yêu cầu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $(document).ready(function () {
            displayOption();
        })
        $('input[name="merchant_audit"]').change(function () {
            displayOption();
        })
        function displayOption() {
            let v = $('input[name="merchant_audit"]:checked').val();
            $('.box-option').addClass('d-none');
            $('.option-' + v).removeClass('d-none');
        }
        $(function () {
            flatpickr('#merchant_active_date', {
                locale: Vietnamese,
                dateFormat: "d/m/Y",
                defaultDate: "{{old('merchant_active_date', $dateActive)}}"
            })
        })
    </script>

@endsection
