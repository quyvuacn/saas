@extends('merchant::layouts.master')

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Profile') }}</h1>

    @include('merchant::layouts.partials.header-error')

    @include('merchant::layouts.partials.header-message')

    <?php $merchant = auth(MERCHANT)->user(); ?>
    <div class="row">
        <div class="col-lg-4 order-lg-2">
            <div class="card shadow mb-4">
                <div class="card-profile-image mt-4">
                    <figure class="rounded-circle avatar avatar font-weight-bold" style="font-size: 60px; height: 180px; width: 180px;" data-initial="{{substr($merchant->name,0,1) ?? '---'  }}"></figure>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <h5 class="font-weight-bold">{{ $merchant->name }}</h5>
                                <p>Administrator</p>
                            </div>
                        </div>
                    </div>
                    <?php /* ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card-profile-stats">
                                <span class="heading">22</span>
                                <span class="description">Friends</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-profile-stats">
                                <span class="heading">10</span>
                                <span class="description">Photos</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card-profile-stats">
                                <span class="heading">89</span>
                                <span class="description">Comments</span>
                            </div>
                        </div>
                    </div>
                    <?php */ ?>
                </div>
            </div>
        </div>
        <div class="col-lg-8 order-lg-1">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tài khoản của tôi</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('merchant.account.profile') }}" autocomplete="off">
                        @csrf
                        <h6 class="heading-small text-muted mb-4">Thông tin tài khoản</h6>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="name">Họ & tên</label>
                                    <input type="text" id="name" class="form-control {{$errors->has('name')?'is-invalid':''}}" name="name" placeholder="Họ tên" value="{{ old('name',$merchant->name) }}">
                                </div>
                            </div>
                        </div>
                        @if($merchant->isSuperAdmin())
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label" for="email">Email<span class="small text-danger">*</span></label>
                                        <input type="email" id="email" class="form-control {{$errors->has('email')?'is-invalid':''}}" name="email" placeholder="example@example.com" value="{{ old('email', $merchant->email) }}">
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label" for="email">Email<span class="small text-danger">*</span></label>
                                        <input type="email" id="email" class="form-control " disabled name="email" placeholder="" value="{{$merchant->email}}">
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="new_password">Mật khẩu</label>
                                    <input type="password" id="new_password" class="form-control {{$errors->has('new_password')?'is-invalid':''}}" name="new_password" placeholder="Mật khẩu mới">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group focused">
                                    <label class="form-control-label" for="password_confirmation">Xác nhận mật khẩu</label>
                                    <input type="password" id="password_confirmation" class="form-control {{$errors->has('password_confirmation')?'is-invalid':''}}" name="password_confirmation" placeholder="Xác nhận mật khẩu">
                                </div>
                            </div>
                        </div>
                        <!-- Button -->
                        <div class="row">
                            <div class="col text-center">
                                <button type="submit" class="btn btn-primary btn-block">Cập nhật</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
