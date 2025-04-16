@extends('layouts.auth')

@section('main-content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-xl-10 col-lg-12 col-md-9">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-6 d-none d-lg-block bg-password-image bg-login-image"></div>
                        <div class="col-lg-6">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">{{ __('Xác thực địa chỉ Email') }}</h1>
                                </div>

                                @if (session('resent'))
                                    <div class="alert alert-success border-left-success" role="alert">
                                        {{ __('Một liên kết xác minh mới đã được gửi đến địa chỉ email của bạn. Vui lòng kiểm tra Email') }}
                                    </div>
                                @endif

                                {{ __('Trước khi tiếp tục, vui lòng kiểm tra email của bạn để biết liên kết xác minh.') }}
                                {{ __('Nếu bạn không nhận được email') }}, <a href="{{ route('verification.resend') }}">{{ __('bấm vào đây để yêu cầu lại') }}</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
