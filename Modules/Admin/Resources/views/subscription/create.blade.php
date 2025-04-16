@extends('admin::layouts.master')

@section('main-content')


    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Yêu cầu gia hạn thuê bao') }}</h1>

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
        <div class="col-lg-12">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tạo mới yêu cầu gia hạn thuê bao</h6>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.subscription.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Chọn Merchant</label>
                            <select class="form-control" name="merchant_id" required>
                                <option value="0">Chọn merchant</option>
                                @foreach($merchants as $merchant)
                                    <option value="{{$merchant['id']}}" @if (!empty(old('merchant_id')) && old('merchant_id') == $merchant['id']) selected @endif>{{$merchant['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Chọn Machine</label>
                            <select class="form-control" name="machine_id" required>
                                <option value="0">Chọn Machine</option>
                                @foreach($machines as $machine)
                                    <option value="{{$machine['id']}}" @if (!empty(old('machine_id')) && old('machine_id') == $machine['id']) selected @endif>{{$machine['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Số tiền thanh toán</label>
                            <input name="request_price" type="text" class="form-control" placeholder="Nhập số tiền cần thanh toán" value="{{old('request_price')}}" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Số tháng gia hạn</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="Nhập số tháng gia hạn thuê bao" name="request_month" value="{{old('request_month')}}" required>
                                <div class="float-left input-group-prepend">
                                    <div class="input-group-text">Tháng</div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Tùy chỉnh ngày hết hạn</label>
                            <input type="date" class="form-control" name="date_expire_option" value="{{old('date_expire_option')}}">
                        </div>
                        <button type="submit" class="btn btn-facebook btn-block"><i class="fas fa-check"></i> Tạo yêu cầu gia hạn</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('extra-js')
    <script>
        var arrMerchant = {!! json_encode($merchants) !!};
        var arrMachine = {!! json_encode($machines) !!};

        $(document).ready(function () {
            $('select[name=merchant_id]').on('change', function () {
                var valMerchant = $(this).find(':selected').val();
                var option = '<option value="0">Chọn Machine</option>';
                $.each(arrMachine, function (index, value) {
                    if(valMerchant == 0){
                        option += "<option value='" + value.id + "'>" + value.name + "</option>";
                    } else if(valMerchant != 0 && value.merchant_id == valMerchant){
                        option += "<option value='" + value.id + "'>" + value.name + "</option>";
                    }
                })
                $('select[name=machine_id]').find('option')
                    .remove()
                    .end()
                    .empty()
                    .append(option)
            })

            $('select[name=machine_id]').on('change', function () {
                if($('select[name=merchant_id]').find(':selected').val() != 0){
                    return;
                }
                var valMachine = $(this).find(':selected').val();
                var merchantId = 0;
                $.each(arrMachine, function (index, value) {
                    if(valMachine == value.id && value.merchant_id != null){
                        merchantId = value.merchant_id;
                        return true;
                    }
                })
                $('select[name=merchant_id]').val(merchantId);
            })
        })
    </script>
@endsection
