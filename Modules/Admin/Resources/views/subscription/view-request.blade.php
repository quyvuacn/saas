@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Duyệt yêu cầu gia hạn thuê bao') }}</h1>

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
                            Merchant: <strong>{{$subscriptionRequest->merchant ? $subscriptionRequest->merchant->name : ''}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Hạn sử dụng hiện tại:
                            <strong>{{!empty($subscripton->date_expiration) ? date('d/m/Y', strtotime($subscripton->date_expiration)) : ''}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Số tháng yêu cầu nạp thêm: <strong class="text-danger">{{$subscriptionRequest->request_month}} tháng</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Hạn sử dụng sau khi nạp thêm:
                            <strong>{{date('d/m/Y', strtotime($dateExpireOption))}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Số
                            điện thoại liên hệ: <a href="tel:{{$subscriptionRequest->merchant ? $subscriptionRequest->merchant->phone : ''}}" class="font-weight-bold">{{$subscriptionRequest->merchant ? $subscriptionRequest->merchant->phone : ''}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Email
                            liên hệ: <a class="font-weight-bold" href="mailto:{{$subscriptionRequest->merchant ? $subscriptionRequest->merchant->email : ''}}">{{$subscriptionRequest->merchant ? $subscriptionRequest->merchant->email : ''}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Ngày
                            tạo yêu cầu: <strong>{{date('H:i d/m/Y', strtotime($subscriptionRequest->created_at))}}</strong></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Số tiền cần thanh toán: <strong class="text-danger">{{number_format($subscriptionRequest->request_price)}} VNĐ</strong></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Hình thức thanh toán:
                            <strong>{{!empty($paymentMethod[$subscriptionRequest->payment_method]) ? $paymentMethod[$subscriptionRequest->payment_method] : ''}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Tình
                            trạng yêu cầu:
                            @if($subscriptionRequest->status == $subscriptionRequest::REQUEST_SUCCESS)
                                <label class="badge badge-info">Hoàn tất</label>
                            @elseif($subscriptionRequest->status == $subscriptionRequest::REQUEST_CANCEL)
                                <label class="badge badge-danger">Đã hủy</label>
                            @else
                                <label class="badge badge-primary">Chờ duỵêt</label>
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
                    <form>
                        <div class="box-option-advance form-group">
                            <div class="option-0">
                                <div class="form-group">
                                    <label for="exampleFormControlInput2">Tùy chỉnh hạn sử dụng sau khi nạp thêm</label>
                                    <input class="form-control" type="date" name="date_expire_option" id="date_expire_option" value="{{$dateExpireOption}}" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="exampleFormControlInput2">Tùy chỉnh số tiền khách đã chuyển</label>
                                    <input type="float" name="request_price" class="form-control" placeholder="Nhập số tiền đã nhận chuyển khoản của khách" value="{{$subscriptionRequest->request_price}}" disabled>
                                </div>
                            </div>
                            <div class="form-group mt-4 option-2">
                                <label for="exampleFormControlTextarea1">Thông tin giao dịch tài chính</label>
                                <textarea class="form-control" rows="3" name="other_info" disabled>{{$subscriptionRequest->other_info}}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
