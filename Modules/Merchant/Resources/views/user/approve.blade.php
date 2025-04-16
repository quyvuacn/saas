@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Duyệt tín dụng cho người dùng') }}</h1>

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
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin người dùng</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Email: <strong>{{$approve->email ?? '---' }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">Ngày
                            tạo tài khoản:
                            <strong>{{$approve->created_at ? $approve->created_at->format('H:i d/m/Y') : '---' }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Hạn mức đã cấp:
                            <strong>{{number_format($approve->credit_quota ?? 0)}} Coin</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Số dư hiện tại:
                            <strong>{{number_format($approve->coin ?? 0)}} Coin</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Ngày cấp hạn mức:
                            <strong>{{$approve->credit_updated_at ? $approve->credit_updated_at->format('H:i d/m/Y') : '---' }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Người cấp hạn mức:
                            <strong>{{$approve->merchantUpdateBy ? $approve->merchantUpdateBy->name: '---' }}</strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Duyệt tín dụng</h6>
                </div>
                <div class="card-body">
                    <form action="{{route('merchant.user.approve',['approve'=>$approve->id])}}" method="POST">
                        @csrf
                        <div class="alert alert-info">
                            Kiểm tra thông tin tài khoản để chắc chắn rằng tín dụng được cấp đúng với quy định của đơn vị.
                        </div>
                        <?php

                        $quota = old('credit_quota') ?? ($approve->credit_quota ?? 0);

                        ?>
                        <div class="form-group credit-quota">
                            <label for="credit_quota">Thay đổi Hạn mức tín dụng</label>
                            <input type="number" class="form-control" name="credit_quota" id="credit_quota" placeholder="Nhập hạn mức tín dụng được cấp" value="{{ $quota }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-user btn-block">
                            Cập nhật tín dụng
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
