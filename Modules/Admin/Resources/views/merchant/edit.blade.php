@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Sửa thông tin Merchant') }}</h1>

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
                    <h6 class="m-0 font-weight-bold text-primary">Thêm các máy bán hàng mới nhập về</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{route('admin.merchant.store', ['merchantId' => $merchant->id])}}">
                        @csrf
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Tên Merchant</label>
                            <input type="text" name="name" class="form-control" placeholder="Nhập tên của merchant.." value="{{old('name', $merchant->name)}}" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Nhập email</label>
                            <input type="email" name="email" class="form-control" placeholder="Nhập email.." value="{{old('email', $merchant->email)}}" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Nhập số điện thoại</label>
                            <input type="tel" name="phone" class="form-control" pattern="[0-9]{}" placeholder="Nhập số điện thoại.." value="{{old('phone', $merchant->phone)}}" required>
                        </div>
                        @if ($merchant->parent_id != 0)
                            @php $merchant = $merchant->commonMerchant() @endphp
                        @endif
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Nhập tên công ty</label>
                            <input type="text" name="merchant_company" class="form-control" placeholder="Nhập tên công ty.." value="{{old('merchant_company', $merchant->merchantInfo->merchant_company)}}" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Nhập địa chỉ</label>
                            <input type="text" name="merchant_address" class="form-control" placeholder="Nhập địa chỉ công ty.." value="{{old('merchant_address', $merchant->merchantInfo->merchant_address)}}" required>
                        </div>
                        <button type="submit" class="btn btn-facebook btn-block"><i class="fas fa-check"></i> Update thông tin Merchant</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
