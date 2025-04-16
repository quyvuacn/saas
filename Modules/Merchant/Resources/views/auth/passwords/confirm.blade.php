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
                                        <h1 class="h4 text-gray-900 mb-4">{{ __('Xác nhận mật khẩu') }}</h1>
                                        <p>{{ __('Vui lòng xác nhận mật khẩu của bạn trước khi tiếp tục.') }}</p>
                                    </div>

                                    @if ($errors->any())
                                        <div class="alert alert-danger border-left-danger" role="alert">
                                            <ul class="pl-4 my-2">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if (session('status'))
                                        <div class="alert alert-success border-left-success" role="alert">
                                            {{ session('status') }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('merchant.password.confirm') }}" class="user">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" name="password" placeholder="{{ __('Mật khẩu') }}" required autocomplete="current-password">
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                                {{ __('Xác nhận mật khẩu') }}
                                            </button>
                                        </div>
                                    </form>

                                    <hr>

                                    @if (Route::has('merchant.password.forgot'))
                                        <div class="text-center">
                                            <a class="small" href="{{ route('merchant.password.forgot') }}">
                                                {{ __('Quên mật khẩu?') }}
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
