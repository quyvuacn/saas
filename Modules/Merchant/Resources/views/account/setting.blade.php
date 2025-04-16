@extends('merchant::layouts.master')

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Cài đặt chung') }}</h1>

    @include('merchant::layouts.partials.header-error')
    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12 order-lg-1">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cài đặt tài khoản Merchant</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('merchant.account.setting') }}" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="name">Tên merchant
                                        <span class="small text-danger">*</span></label>
                                    <input type="text" id="name" class="form-control {{$errors->has('name')?'is-invalid':''}}" name="name" placeholder="Nhập tên hiển thị của Merchant" value="{{ old('name', $merchant->name) }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="company">Tên công ty
                                        <span class="small text-danger">*</span></label>
                                    <input type="text" id="company" class="form-control {{$errors->has('company')?'is-invalid':''}}" name="company" placeholder="Nhập tên doanh nghiệp của bạn" value="{{ old('company', $merchantInfo->merchant_company ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="company_address">Địa chỉ doanh nghiệp
                                        <span class="small text-danger">*</span></label>
                                    <input type="text" id="company_address" class="form-control {{$errors->has('company_address')?'is-invalid':''}}" name="company_address" placeholder="Địa chỉ doanh nghiệp của bạn" value="{{ old('company_address', $merchantInfo->merchant_address ?? '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="dept_collection_date">Ngày quyết toán tín dụng của người dùng
                                        <span class="small text-danger">*</span></label>
                                    <input type="number" id="dept_collection_date" class="form-control {{$errors->has('dept_collection_date')?'is-invalid':''}}" name="dept_collection_date" placeholder="Nhập ngày thu hồi tiền nợ của khách" value="{{old('dept_collection_date', $merchantInfo->merchant_dept_collection_date ?? 15)}}" min="1" max="31">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="website">Website
                                        <span class="small text-danger">*</span></label>
                                    <input type="text" id="website" class="form-control {{$errors->has('website')?'is-invalid':''}}" name="website" placeholder="Website" value="{{old('website', $merchantInfo->website)}}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="phone">Hotline
                                        <span class="small text-danger">*</span></label>
                                    <input type="text" id="phone" class="form-control {{$errors->has('phone')?'is-invalid':''}}" name="phone" placeholder="Hotline" value="{{old('phone', $merchantInfo->phone)}}">
                                </div>
                            </div>
                        </div>
                        <?php
                        $checked_address_1 = '';
                        $checked_address_2 = '';
                        if (old('checkout_address')) {
                            if (old('checkout_address') == 1) {
                                $checked_address_1 = '';
                                $checked_address_2 = 'checked';
                            } else {
                                $checked_address_1 = 'checked';
                                $checked_address_2 = '';
                            }
                        } else {
                            if (!empty($merchantInfo->merchant_other_address)) {
                                $checked_address_1 = '';
                                $checked_address_2 = 'checked';
                            } else {
                                $checked_address_1 = 'checked';
                                $checked_address_2 = '';
                            }
                        }
                        ?>
                        <div class="form-check" id="current-address">
                            <input class="form-check-input" type="radio" name="checkout_address" id="checkout_address_1" {{$checked_address_1}} value="0">
                            <label class="form-check-label" for="checkout_address_1">
                                Dùng địa chỉ doanh nghiệp làm nơi nhận thanh toán tại văn phòng
                            </label>
                        </div>
                        <div class="form-check mb-3" id="other-address">
                            <input class="form-check-input" type="radio" name="checkout_address" id="checkout_address_2" {{$checked_address_2}} value="1">
                            <label class="form-check-label" for="checkout_address_2">
                                Chọn địa chỉ khác làm nơi thanh toán tại văn phòng
                            </label>
                        </div>
                        <?php
                        $show = 'none';
                        if (old('checkout_address')) {
                            if (old('checkout_address') == 1) {
                                $show = 'block';
                            } else {
                                $show = 'none';
                            }
                        } else {
                            if (!empty($merchantInfo->merchant_other_address)) {
                                $show = 'block';
                            } else {
                                $show = 'none';
                            }
                        }
                        ?>
                        <div class="form-group address-block" style="display: {{$show}}">
                            <label for="other_address_input">Địa chỉ khác</label>
                            <input type="text" value="{{old('other_address_input', $merchantInfo->merchant_other_address)}}" name="other_address_input" id="other_address_input" placeholder="Nhập địa chỉ..." class="form-control {{$errors->has('other_address_input')?'is-invalid':''}}">
                        </div>
                        <h4 class="mt-5">Thông tin tài khoản thanh toán bằng chuyển khoản</h4>
                        <div id="bank-account-list" class="row">
                            {{--NEW ADDED BANK--}}
                            @if (old('bank_account_number'))
                                @for ($i = 0; $i < old('bank_account_number'); $i++)
                                    @php($offset = old('bank_account_number') - $bankAccountNumber)
                                    <div class="col-lg-6 mb-3">
                                        <div class="bank-item card shadow mb-4">
                                            <div class="card-header py-3">
                                                <h6 class="m-0 font-weight-bold text-primary">Thông tin tài khoản ngân hàng</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group">
                                                    @if($i < $offset)
                                                        @php($bank_name = old('bank_name.'.$i))
                                                        @php($benefit_name = old('benefit_name.'.$i))
                                                        @php($bank_number = old('bank_number.'.$i))
                                                    @endif
                                                    @if($i >= $offset)
                                                        @php($bank_name = old('bank_name.'.$i, $bankInfo[$i - $offset]->bank_name))
                                                        @php($benefit_name = old('benefit_name.'.$i, $bankInfo[$i - $offset]->benefit_name))
                                                        @php($bank_number = old('bank_number.'.$i, $bankInfo[$i - $offset]->bank_number))
                                                    @endif
                                                    <li class="list-group-item">
                                                        <div class="bank-name d-flex">
                                                            <span>Tên ngân hàng</span><input type="text" name="bank_name[]" placeholder="Tên ngân hàng thanh toán..." class="form-control {{$errors->has('bank_name.'.$i)?'is-invalid':''}}" value="{{$bank_name}}">
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="benefit-name d-flex">
                                                            <span>Tên người thụ hưởng</span><input type="text" name="benefit_name[]" placeholder="Tên người thụ hưởng..." class="form-control {{$errors->has('benefit_name.'.$i)?'is-invalid':''}}" value="{{$benefit_name}}">
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="bank-number d-flex">
                                                            <span>Số tài khoản</span><input type="text" name="bank_number[]" placeholder="Số tài khoản..." class="form-control {{$errors->has('bank_number.'.$i)?'is-invalid':''}}" value="{{$bank_number}}">
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endfor
                            @else
                                @if($bankAccountNumber < 1)
                                    <div class="col-lg-6 mb-3">
                                        <div class="bank-item card shadow mb-4">
                                            <div class="card-header py-3">
                                                <h6 class="m-0 font-weight-bold text-primary">Thông tin tài khoản ngân hàng</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group">
                                                    <li class="list-group-item">
                                                        <div class="bank-name d-flex">
                                                            <span>Tên ngân hàng</span><input type="text" name="bank_name[]" placeholder="Tên ngân hàng thanh toán..." class="form-control {{$errors->has('bank_name.0')?'is-invalid':''}}" value="{{old('bank_name.0')}}">
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="benefit-name d-flex">
                                                            <span>Tên người thụ hưởng</span><input type="text" name="benefit_name[]" placeholder="Tên người thụ hưởng..." class="form-control {{$errors->has('benefit_name.0')?'is-invalid':''}}" value="{{old('benefit_name.0')}}">
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item">
                                                        <div class="bank-number d-flex">
                                                            <span>Số tài khoản</span><input type="text" name="bank_number[]" placeholder="Số tài khoản..." class="form-control {{$errors->has('bank_number.0')?'is-invalid':''}}" value="{{old('bank_number.0')}}">
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @for ($j = 0; $j < $bankAccountNumber; $j++)
                                        <div class="col-lg-6 mb-3">
                                            <div class="bank-item card shadow mb-4">
                                                <div class="card-header py-3">
                                                    <h6 class="m-0 font-weight-bold text-primary">Thông tin tài khoản ngân hàng</h6>
                                                </div>
                                                <div class="card-body">
                                                    <ul class="list-group">
                                                        @php($bank_name = $bankInfo[$j]->bank_name)
                                                        @php($benefit_name = $bankInfo[$j]->benefit_name)
                                                        @php($bank_number = $bankInfo[$j]->bank_number)
                                                        <li class="list-group-item">
                                                            <div class="bank-name d-flex">
                                                                <span>Tên ngân hàng</span><input type="text" name="bank_name[]" placeholder="Tên ngân hàng thanh toán..." class="form-control {{$errors->has('bank_name.'.$j)?'is-invalid':''}}" value="{{$bank_name}}">
                                                            </div>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <div class="benefit-name d-flex">
                                                                <span>Tên người thụ hưởng</span><input type="text" name="benefit_name[]" placeholder="Tên người thụ hưởng..." class="form-control {{$errors->has('benefit_name.'.$j)?'is-invalid':''}}" value="{{$benefit_name}}">
                                                            </div>
                                                        </li>
                                                        <li class="list-group-item">
                                                            <div class="bank-number d-flex">
                                                                <span>Số tài khoản</span><input type="text" name="bank_number[]" placeholder="Số tài khoản..." class="form-control {{$errors->has('bank_number.'.$j)?'is-invalid':''}}" value="{{$bank_number}}">
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                @endif
                            @endif
                            <div class="col-lg-6 mb-3 add-bank-account">
                                <a href="javascript:void(0)">
                                    <div class="card shadow mb-4">
                                        <div class="card-body text-center">
                                            <span class="icon" style="font-size: 80px;"><i class="fas fa-plus"></i></span>
                                            <p style="font-size: 30px;">
                                                Thêm tài khoản thanh toán
                                            </p></div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <input type="hidden" name="bank_account_number" value="{{old('bank_account_number', $bankAccountNumber > 0 ? $bankAccountNumber : 1)}}">
                        <!-- Button -->
                        <div class="row">
                            <div class="col text-center">
                                <button type="submit" class="btn btn-facebook btn-block">
                                    <i class="fas fa-check"></i> Cập nhật thông tin
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 mb-3 bank-account-clone" style="display: none">
        <div class="bank-item card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Thông tin tài khoản ngân hàng</h6>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        <div class="bank-name d-flex">
                            <span>Tên ngân hàng</span><input type="text" name="bank_name[]" placeholder="Tên ngân hàng thanh toán..." class="form-control">
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="benefit-name d-flex">
                            <span>Tên người thụ hưởng</span><input type="text" name="benefit_name[]" placeholder="Tên người thụ hưởng..." class="form-control">
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="bank-number d-flex">
                            <span>Số tài khoản</span><input type="text" name="bank_number[]" placeholder="Số tài khoản..." class="form-control">
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

@endsection

@section('extra-js')
    <script>
        $(function () {
            $('#other-address').click(function (e) {
                $('.address-block').slideDown();
            })
            $('#current-address').click(function (e) {
                $('.address-block').slideUp();
            });

            if ($('#checkout_address_2').is(':checked')) {
                $('.address-block').slideDown();
            } else {
                $('.address-block').slideUp();
            }

            $('.add-bank-account a').click(function (e) {
                e.preventDefault();
                var childs = $('#bank-account-list').children().length;
                if (childs > 6) {
                    alert('Số lượng tài khoản đã vượt quá số lượng cho phép!');
                    return false;
                }
                var clone = $('.bank-account-clone').clone();
                $(clone).show();
                $(clone).removeClass('bank-account-clone');
                $('#bank-account-list').prepend(clone);
                $('input[name="bank_account_number"]').val(parseInt($('input[name="bank_account_number"]').val()) + 1);
            });
        })
    </script>
@endsection
