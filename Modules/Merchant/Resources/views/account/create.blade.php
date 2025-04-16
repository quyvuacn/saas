@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ !isset($account) ? __( 'Tạo tài khoản Merchant') : __('Sửa tài khoản Merchant')}}</h1>

    @include('merchant::layouts.partials.header-error')

    <div class="row">
        <div class="col-lg-12">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin tài khoản phụ</h6>
                </div>
                <div class="card-body">
                    <form action="{{isset($account) ? route('merchant.account.edit', ['account'=>$account]) :  route('merchant.account.create')}}" method="POST">
                        @csrf
                        <input type="hidden" name="table" value="merchant">
                        <div class="form-group">
                            @php
                                $oldName = old('name')?old('name'):($account->name ?? '');
                            @endphp
                            <label for="name">Tên tài khoản</label>
                            <input type="text" class="form-control {{$errors->has('name')?'is-invalid':''}}" name="name" id="name" placeholder="Nhập tên tài khoản..." value="{{$oldName}}" required>
                        </div>
                        <div class="form-group">
                            @php
                                $oldMail = old('email')?old('email'):($account->email ?? '');
                            @endphp
                            <label for="email">Email</label>
                            <input type="email" class="form-control {{$errors->has('email')?'is-invalid':''}}" name="email" id="email" placeholder="Nhập email.." value="{{ $oldMail }}" required>
                        </div>
                        <?php
                        if (isset($account)) {
                            $myPermissions = $account->permissions->pluck('id')->toArray();
                        }
                        ?>
                        @if(isset($roles) && $roles)
                            <div class="form-group">
                                <label for="">Phân quyền</label>
                                <div class="row m-0 role-group {{$errors->has('permissions')?'is-invalid':''}}">
                                    @foreach($roles as $key => $role)
                                        @if($role->id !== 1)
                                            <div class="custom-control custom-checkbox mb-3 small col-md-3 col-6">
                                                <label class=""><strong>{{$role->name}}</strong></label>
                                                <div class="permission-list pl-4">
                                                    @if($role->permissions)
                                                        @foreach($role->permissions as $permission)
                                                            <?php
                                                            $checked = '';
                                                            if (old('permissions') && in_array($permission->id,
                                                                    old('permissions'))) {
                                                                $checked = 'checked';
                                                            } elseif (!old('permissions')) {
                                                                if (isset($myPermissions) && isset($account) && in_array($permission->id,
                                                                        $myPermissions)) {
                                                                    $checked = 'checked';
                                                                }
                                                            }
                                                            ?>
                                                            <p>
                                                                <input type="checkbox" class="custom-control-input" value="{{$permission->id}}" {{$checked}} name="permissions[]" id="role_{{$permission->id}}">
                                                                <label class="custom-control-label" for="role_{{$permission->id}}">{{$permission->permission_desc}}</label>
                                                            </p>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @if(!isset($account))
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
                        @endif
                        <?php

                        $display = 'none';
                        $display = isset($account) ? 'block' : 'none';
                        if (isset($account)) {
                            $display = 'block';
                        } else {
                            if (old('pass_make') == 1) {
                                $display = 'block';
                            }
                        }
                        ?>
                        <div class="form-group password-block" style="display:{{$display}}">
                            <label for="password">Nhập mật khẩu {{isset($account) ? 'mới':''}}</label>
                            <input type="password" class="form-control {{$errors->has('password')?'is-invalid':''}}" value="{{old('password')}}" name="password" id="password" placeholder="Nhập mật khẩu {{isset($account) ? 'mới':''}}...">
                        </div>
                        <button type="submit" class="btn btn-facebook btn-block">
                            <i class="fas fa-check"></i> {{ !isset($account) ? __( 'Tạo tài khoản') : __('Sửa tài khoản')}}
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
                @if(!isset($account))
                $('.password-block').slideUp();
                @endif
            }
        })
    </script>
@endsection
