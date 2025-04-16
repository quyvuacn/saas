@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Duyệt yêu cầu máy bán hàng') }}</h1>

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
                            Merchant: <strong>{{$merchantRequest->merchant->name}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Số máy yêu cầu: <strong>{{$merchantRequest->machine_request_count}}</strong></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Nội dung yêu cầu: <strong>{{$merchantRequest->title}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Số
                            Điện thoại liên hệ: <a href="tel:{{$merchantRequest->merchant->phone}}" class="font-weight-bold">{{$merchantRequest->merchant->phone}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Email
                            liên hệ: <a class="font-weight-bold" href="mailto:{{$merchantRequest->merchant->email}}">{{$merchantRequest->merchant->email}}</a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"><span style="width: 200px">Địa
                                chỉ:</span> <strong class="text-right">{{$merchantRequest->machine_position}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Ngày
                            tạo yêu cầu: <strong>{{date('H:i d/m/Y', strtotime($merchantRequest->created_at))}}</strong></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Ngày
                            nhận máy: <strong>{{date('d/m/Y', strtotime($merchantRequest->created_at))}}</strong></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Tình
                            trạng yêu cầu:
                            @if($merchantRequest->status == $merchantRequest::REQUEST_WAITING_AUDIT)
                                <span class="badge badge-dark">Chờ ký hợp đồng</span>
                            @else
                                <span class="badge badge-primary">Yêu cầu mới</span>
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
                    <form id="form-request" action="{{route('admin.machine.approveRequest', ['merchantRequest' => $merchantRequest->id])}}" method="POST">
                        @csrf
                        <div class="alert alert-info">
                            Hãy gọi điện cho merchant để thẩm định lại yêu cầu, và xin thêm các yêu cầu
                            khác.
                        </div>
                        <h5>Điền các thông tin liên quan như sau:</h5>

                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="merchant_audit" id="exampleRadios1" value="1" @if(old('merchant_audit', 1) == 1) checked="checked" @endif>
                            <label class="form-check-label" for="exampleRadios1">
                                @if($merchantRequest->status == $merchantRequest::REQUEST_WAITING_AUDIT)
                                    Ký hợp đồng thành công, đang chuyển máy
                                @else
                                    Thẩm định thành công, ký kết hợp đồng.
                                @endif
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="merchant_audit" id="exampleRadios2" value="2" @if(old('merchant_audit') == 2) checked="checked" @endif>
                            <label class="form-check-label" for="exampleRadios2">
                                @if($merchantRequest->status == $merchantRequest::REQUEST_WAITING_AUDIT)
                                    Ký hợp đồng không thành công, hủy yêu cầu.
                                @else
                                    Thẩm định không thành công, hủy yêu cầu.
                                @endif
                            </label>
                        </div>
                        <div class="option-1">
                            <div class="form-group">
                                <label for="exampleFormControlTextarea1">Số lượng máy</label>
                                <input type="number" class="form-control" name="machine_request_count" placeholder="Nhập số lượng máy sẽ cung cấp" value="{{old('machine_request_count', $merchantRequest->machine_request_count)}}">
                            </div>
                            <div class="form-group mt-4">
                                <label for="exampleFormControlTextarea1">Ngày nhận máy</label>
                                <input type="date" class="form-control" name="machine_date_receive" id="machine_date_receive" placeholder="DD/MM/YYYY" value="{{old('machine_date_receive', date('d/m/Y', strtotime($merchantRequest->machine_date_receive)))}}">
                            </div>
                            <div class="form-group mt-4">
                                <label for="exampleFormControlTextarea1">Các yêu cầu khác</label>
                                <textarea class="form-control" name="machine_other_request" rows="3">{{old('machine_other_request', $merchantRequest->machine_other_request)}}</textarea>
                            </div>
                        </div>
                        <div class="form-group mt-4 option-2" style="display: none;">
                            <label for="exampleFormControlTextarea1">Lý do</label>
                            <textarea class="form-control" name="reason" rows="3">{{old('reason')}}</textarea>
                        </div>
                        <button class="btn btn-primary btn-user btn-block">
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
        flatpickr('#machine_date_receive', {
            locale: Vietnamese,
            dateFormat: "d/m/Y",
            defaultDate: "{{old('machine_date_receive', date('d/m/Y', strtotime($merchantRequest->created_at)))}}"
        })
    })

    $(document).ready(function () {
        changeOption();
    })

    $('input[name="merchant_audit"]').change(function () {
        changeOption()
    })

    function changeOption() {
        var v = $('input[name="merchant_audit"]:checked').val();
        if(v == 1){
            setTimeout(function () {
                $('.option-1').slideDown()
            }, 500)
            $('.option-2').slideUp()
        } else{
            $('.option-1').slideUp()
            setTimeout(function () {
                $('.option-2').slideDown()
            }, 500)
        }
    }
</script>

@endsection
