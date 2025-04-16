@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Thêm khách hàng mới') }}</h1>

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
                    <h6 class="m-0 font-weight-bold text-primary">Thêm khách mới</h6>
                </div>
                <div class="card-body">
                    <form action="{{route('merchant.user.create')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control {{$errors->has('email')?'is-invalid':''}}" name="email" id="email" placeholder="Nhập email.." value="{{ old('email') }}" required>
                        </div>
                        <div class="form-check" id="close-password">
                            <input class="form-check-input" type="radio" name="pass_make" id="pass_make_1" {{old('pass_make') == 0 ? 'checked' : ''}} value="{{old('pass_make') ?? 0}}">
                            <label class="form-check-label" for="pass_make_1">
                                Tự tạo mật khẩu, gửi qua email
                            </label>
                        </div>
                        <div class="form-check mb-3" id="open-password">
                            <input class="form-check-input" type="radio" name="pass_make" id="pass_make_2" {{old('pass_make') == 1 ? 'checked' : ''}} value="{{old('pass_make') ?? 1}}">
                            <label class="form-check-label" for="pass_make_2">
                                Đặt mật khẩu
                            </label>
                        </div>
                        <?php
                        $display = 'none';
                        if (old('pass_make') == 1) {
                            $display = 'block';
                        }
                        ?>
                        <div class="form-group password-block" style="display:{{$display}}">
                            <label for="password">Nhập mật khẩu</label>
                            <input type="password" class="form-control {{$errors->has('password')?'is-invalid':''}}" value="{{old('password')}}" name="password" id="password" placeholder="Nhập mật khẩu...">
                        </div>
                        <button type="submit" class="btn btn-facebook btn-block">
                            <i class="fas fa-check"></i> {{__( 'Tạo tài khoản')}}
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
            $('#open-password').click(function (e) {
                $('.password-block').slideDown();
            })
            $('#close-password').click(function (e) {
                $('.password-block').slideUp();
            });

            if ($('#pass_make_2').is(':checked')) {
                $('.password-block').slideDown();
            } else {
                $('.password-block').slideUp();
            }
        })
    </script>
@endsection
