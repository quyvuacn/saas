@php
use Modules\Admin\Classes\Facades\AdminCan;
@endphp

@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ !isset($account) ? __( 'Tạo tài khoản quản trị') : __('Sửa tài khoản quản trị')}}</h1>

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
                    <h6 class="m-0 font-weight-bold text-primary">Nhập thông tin tài khoản quản trị</h6>
                </div>
                <div class="card-body">
                    <form action="{{isset($account) ? route('admin.account.edit', ['account'=>$account]) :  route('admin.account.create')}}" method="POST">
                        @csrf
                        <input type="hidden" name="table" value="admin">
                        <div class="form-group">
                            @php
                                $oldName = old('name')?old('name'):($account->name ?? '');
                            @endphp
                            <label for="name">Tài khoản đăng nhập</label>
                            <input type="text" class="form-control {{$errors->has('name')?'is-invalid':''}}" name="name" id="name" placeholder="Nhập tên tài khoản..." value="{{$oldName}}" required>
                        </div>
                        @if (empty($account))
                        <div class="form-group">
                            <label for="email">Địa chỉ email</label>
                            <input type="email" class="form-control {{$errors->has('email')?'is-invalid':''}}" name="email" id="email" placeholder="Nhập email.." value="{{ old('email') }}" required>
                        </div>
                        @endif
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
                            <div class="form-check" id="make-password">
                                <input class="form-check-input" type="checkbox" name="pass_make" id="pass_make" {{old('pass_make') == 1 ? 'checked' : ''}} value="{{old('pass_make') ?? 1}}">
                                <label class="form-check-label" for="pass_make">
                                    Tự tạo mật khẩu, gửi qua email
                                </label>
                            </div>

                            <div class="form-check mb-3" id="change-password">
                                <input class="form-check-input" type="checkbox" name="pass_change" id="pass_change" {{old('pass_change') == 1 ? 'checked' : ''}} value="{{old('pass_change') ?? 1}}">
                                <label class="form-check-label" for="pass_change">
                                    Yêu cầu thay đổi mật khẩu khi đăng nhập lần đầu.
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
            $('#make-password').click(function (e) {
                if ($('#pass_make').is(':checked')) {
                    $('.password-block').slideUp();
                } else {
                    $('.password-block').slideDown();
                }
            });

            if ($('#pass_make').is(':checked')) {
                $('.password-block').slideUp();
            } else {
                @if(!isset($account))
                $('.password-block').slideDown();
                @endif
            }
        })
    </script>
@endsection
