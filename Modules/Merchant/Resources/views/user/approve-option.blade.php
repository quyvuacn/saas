@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Duyệt số coin người dùng nạp') }}</h1>

    @include('merchant::layouts.partials.header-message')

    @if (!isset($approve) || !$approve)
        <div class="alert alert-danger border-left-danger alert-dismissible fade show" role="alert">
            {{ __('Không tồn tại User này, hoặc User không có request.') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary float-left">Thông tin người dùng </h6>
                </div>
                <div class="card-body">
                    <div class="mb-5">
                        <form action="{{route('merchant.user.rechargeSearch')}}" method="GET">
                            @csrf
                            <div class="input-group">
                                <input type="email" class="form-control bg-light small" name="s" value="{{old('s') ? old('s') : request('s')}}" placeholder="Nhập email người dùng" aria-label="Search" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search fa-sm"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    @if(isset($approve) && $approve)
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Email: <strong>{{$approve->user ? $approve->user->email : '---' }}<br>
                                </strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">Ngày
                                tạo tài khoản:
                                <strong>{{$approve->user ? $approve->user->created_at->format('H:i d/m/Y') : '---' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Hạn mức đã cấp:
                                <strong>{{number_format($approve->user ? $approve->user->credit_quota : 0)}}
                                    <small> coin</small></strong></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Dự nợ/số dư hiện tại:
                                <strong class="text-danger">{{number_format($approve->user ? $approve->user->coin : 0)}}
                                    <small> coin</small></strong></li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Ngày cấp hạn mức:
                                <strong>{{$approve->user && $approve->user->credit_updated_at ? $approve->user->credit_updated_at->format('H:i d/m/Y') : '---' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Người cấp hạn mức:
                                <strong>{{$approve->user && $approve->user->merchantUpdateBy ? $approve->user->merchantUpdateBy->name: '---' }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Số coin yêu cầu nạp: <strong>{{number_format($approve->coin ?? 0)}}
                                    <small>coin</small></strong></li>
                        </ul>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Duyệt tùy chỉnh số coin</h6>
                </div>
                <div class="card-body">
                    <form action="{{$approve && isset($approve) ? route('merchant.user.approveOption', ['approve'=>$approve]) : ''}}" method="POST">
                        @csrf
                        <div class="alert alert-info">
                            Kiểm tra thông tin chuyển tiền của người dùng, để đảm bảo tính chính xác của
                            giao dịch.
                        </div>
                        <h5>Điền các thông tin liên quan như sau:</h5>
                        <?php
                        $user_coin = old('user_coin') ?? number_format($approve->coin ?? 0)
                        ?>
                        <div class="form-group">
                            <label for="user_coin">Tùy chỉnh số coin người dùng đã
                                mua</label>
                            <input type="number" class="form-control" name="user_coin" id="user_coin" placeholder="Nhập số coin người dùng đã mua" value="{{$user_coin}}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-user btn-block" {{!isset($approve) || empty($approve) ? 'disabled':''}}>
                            Cập nhật số coin
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

