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

    @php
        if(!empty(old('date_expire_option'))){
            $dateExpireOption = convertDateFlatpickr(old('date_expire_option'), 'Y-m-d');
        }
    @endphp

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
                            @if($subscriptionRequest->status == $subscriptionRequest::REQUEST_WAITING_CONTRACT)
                                <label class="badge badge-dark">Chờ ký hợp đồng</label>
                            @elseif($subscriptionRequest->status == $subscriptionRequest::REQUEST_WAITING_PAYMENT)
                                <label class="badge badge-warning">Chờ thanh toán</label>
                            @else
                                <label class="badge badge-primary">Yêu cầu mới</label>
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
                    <form action="{{route('admin.subscription.approveRequest', ['subscriptionRequest' => $subscriptionRequest->id])}}" method="POST">
                        @csrf
                        <div class="alert alert-info">
                            Kiểm tra thông tin tài khoản để chắc chắn rằng giao dịch đã thực hiện thành công.
                        </div>
                        <h5>Điền các thông tin liên quan như sau:</h5>

                        @foreach($arrInfoStatus[$subscriptionRequest->status] as $key => $info)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="merchant_audit" id="exampleRadios{{$key}}" value="{{$key}}" @if ($key == 0) checked="checked" @endif>
                                <label class="form-check-label" for="exampleRadios{{$key}}">
                                    {{$info}}
                                </label>
                            </div>
                        @endforeach

                        <div class="box-option-advance form-group">
                            <div class="option-0">
                                <div class="form-group">
                                    <label for="exampleFormControlInput2">Tùy chỉnh hạn sử dụng sau khi nạp thêm</label>
                                    <input class="form-control" type="date" name="date_expire_option" id="date_expire_option" value="{{old('date_expire_option', $dateExpireOption)}}">
                                </div>

                                <div class="form-group">
                                    <label for="exampleFormControlInput2">Tùy chỉnh số tiền khách đã chuyển</label>
                                    <input type="float" name="request_price" class="form-control" placeholder="Nhập số tiền đã nhận chuyển khoản của khách" value="{{old('request_price', $subscriptionRequest->request_price)}}">
                                </div>
                            </div>

                            <div class="form-group mt-4 option-2">
                                <label for="exampleFormControlTextarea1">Thông tin giao dịch tài chính</label>
                                <textarea class="form-control" rows="3" name="other_info">{{old('other_info', $subscriptionRequest->other_info)}}</textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <a href="javascript:void(0);" class="option-advance">
                                <em class="fa fa-chevron-right"></em>
                                <span>
                                Hiện tùy chọn nâng cao
                                </span>
                            </a>
                        </div>

                        <button href="login.html" class="btn btn-primary btn-user btn-block mt-3">
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

        $(function () {
            flatpickr('#date_expire_option', {
                locale: Vietnamese,
                dateFormat: "d/m/Y",
                defaultDate: "{{date('d/m/Y', strtotime($dateExpireOption))}}"
            })
        })

        $('.option-advance').on('click', function () {
            $(this).children('em').toggleClass('active');
            if($(this).children('em').hasClass('active')){
                $('.box-option-advance').slideDown();
                $('.option-advance').children('span').text('Ẩn tùy chọn nâng cao');
            } else {
                $('.box-option-advance').slideUp();
                $('.option-advance').children('span').text('Hiện tùy chọn nâng cao');
            }
        })

        $('input[name="merchant_audit"]').change(function () {
            displayOption();
        })

        function displayOption() {
            let v = $('input[name="merchant_audit"]:checked').val();
            if(v == 1){
                $('.option-0').slideUp();
            } else {
                $('.option-0').slideDown();
            }
        }
    </script>
@endsection

<style>
    em.active{
        transform: rotate(90deg);
    }
    em{
        transition: all .3s ease;
    }
    .box-option-advance{
        display: none;
    }
</style>
