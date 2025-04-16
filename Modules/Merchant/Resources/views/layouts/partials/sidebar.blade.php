<?php

use Modules\Admin\Classes\Facades\AdminCan;
use Modules\Merchant\Classes\Facades\MerchantCan;

?>
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('merchant.dashboard')}}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">MERCHANT CMS <sup>v1.0</sup></div>
    </a>
@if (MerchantCan::do('dashboard'))
    <!-- Divider -->
        <hr class="sidebar-divider my-0">
        <!-- Nav Item - Dashboard -->
        <li class="nav-item {{ Nav::isRoute('merchant.dashboard') }}">
            <a class="nav-link" href="{{ route('merchant.dashboard') }}">
                <i class="fas fa-fw fa-th-large"></i>
                <span>{{ __('Dashboard') }}</span></a>
        </li>
@endif
@if (!MerchantCan::do('isApproved'))
    <!-- Divider -->
        <hr class="sidebar-divider my-0">
        <!-- Nav Item - Dashboard -->
        <li class="nav-item {{ Nav::isRoute('merchant.account.profile') }}">
            <a class="nav-link" href="{{ route('merchant.account.profile') }}">
                <i class="fas fa-fw fa-user"></i>
                <span>{{ __('Profile') }}</span></a>
        </li>
    @endif
    @if (MerchantCan::do('isApproved'))
        @if (auth(MERCHANT)->user()->can('user.list') || auth(MERCHANT)->user()->can('user.credit.list')
|| auth(MERCHANT)->user()->can('user.debt.list') || auth(MERCHANT)->user()->can('user.coin.request.list') || auth(MERCHANT)->user()->can('notify.list'))
            {{-- CUSTOMER --}}
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                {{__('Khách hàng')}}
            </div>
        @endif
        @if (auth(MERCHANT)->user()->can('user.list'))
            <li class="nav-item {{ Nav::isRoute('merchant.user.list') }}">
                <a class="nav-link" href="{{route('merchant.user.list')}}">
                    <i class="fas fa-fw fa-user-plus"></i>
                    <span>{{__('Khách hàng đăng ký mới')}}</span>
                </a>
            </li>
        @endif
        @if (auth(MERCHANT)->user()->can('user.credit.list'))
            <li class="nav-item {{ Nav::isRoute('merchant.user.credit') }}">
                <a class="nav-link" href="{{route('merchant.user.credit')}}">
                    <i class="fas fa-credit-card"></i>
                    <span>{{__('Tín dụng của khách')}}</span>
                </a>
            </li>
        @endif
        @if (auth(MERCHANT)->user()->can('user.debt.list'))
            <li class="nav-item {{ Nav::isRoute('merchant.user.debt') }}">
                <a class="nav-link" href="{{route('merchant.user.debt')}}">
                    <i class="fas fa-donate"></i>
                    <span>{{__('Thu hồi tiền nợ')}}</span>
                </a>
            </li>
        @endif
        @if (auth(MERCHANT)->user()->can('user.coin.request.list'))
            <li class="nav-item {{ Nav::isRoute('merchant.user.recharge') }}">
                <a class="nav-link" href="{{route('merchant.user.recharge')}}">
                    <i class="fas fa-donate"></i>
                    <span>{{__('Yêu cầu nạp tiền')}}</span>
                </a>
            </li>
        @endif
        @if (auth(MERCHANT)->user()->can('notify.list'))
            <li class="nav-item {{ Nav::isRoute('merchant.notify.list') }}">
                <a class="nav-link" href="{{route('merchant.notify.list')}}">
                    <i class="fas fa-bell"></i>
                    <span>{{__('Quản lý thông báo')}}</span>
                </a>
            </li>
        @endif
    @endif
    @if (MerchantCan::do('isApproved'))
        @if (auth(MERCHANT)->user()->can('account.list') || auth(MERCHANT)->user()->can('account.edit'))
            {{-- MERCHANT ACCOUNT --}}
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                {{__('Tài khoản')}}
            </div>
        @endif
    <!-- ACCOUNT Menu -->
        @if (auth(MERCHANT)->user()->can('account.list'))
            <li class="nav-item {{ Nav::isRoute('merchant.account.list') }}">
                <a class="nav-link" href="{{route('merchant.account.list')}}">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <span>{{__('Quản lý tài khoản phụ')}}</span>
                </a>
            </li>
        @endif
        @if (auth(MERCHANT)->user()->can('permission.list'))
            <li class="nav-item {{ Nav::isRoute('merchant.account.permission') }}">
                <a class="nav-link" href="{{route('merchant.account.permission')}}">
                    <i class="fas fa-fingerprint"></i>
                    <span>{{__('Phân quyền tài khoản phụ')}}</span>
                </a>
            </li>
        @endif
    @endif
    @if (MerchantCan::do('isApproved'))
        @if (MerchantCan::do('machine_request.list') || MerchantCan::do('machine_request.history.list') || MerchantCan::do('machine.list') || MerchantCan::do('selling.history.list'))
            {{-- MACHINE Menu--}}
            <hr class="sidebar-divider">
            <div class="sidebar-heading">
                {{__('Máy bán hàng')}}
            </div>
        @endif
        @if (MerchantCan::do('machine_request.list'))
            <li class="nav-item {{ Nav::isRoute('merchant.machine.listRequest') }} {{ Nav::isRoute('merchant.machine.request') }}">
                <a class="nav-link" href="{{route('merchant.machine.listRequest')}}">
                    <i class="fas fa-edit"></i>
                    <span>{{__('Yêu cầu máy bán hàng')}}</span>
                </a>
            </li>
        @endif
        @if(MerchantCan::do('machine_request.history.list'))
            <li class="nav-item {{ Nav::isRoute('merchant.machine.requestHistory') }}">
                <a class="nav-link" href="{{route('merchant.machine.requestHistory')}}">
                    <i class="fas fa-tasks"></i>
                    <span>{{__('Lịch sử yêu cầu')}}</span>
                </a>
            </li>
        @endif
        @if(MerchantCan::do('machine.hasAny'))
            @if(MerchantCan::do('machine.list'))
                <li class="nav-item {{ Nav::isRoute('merchant.machine.list') }}">
                    <a class="nav-link" href="{{route('merchant.machine.list')}}">
                        <i class="fas fa-store"></i>
                        <span>{{__('Danh sách máy bán hàng')}}</span>
                    </a>
                </li>
            @endif
            @if(MerchantCan::do('selling.history.list'))
                <li class="nav-item {{ Nav::isRoute('merchant.machine.history') }}">
                    <a class="nav-link" href="{{route('merchant.machine.history')}}">
                        <i class="fas fa-funnel-dollar"></i>
                        <span>{{__('Lịch sử bán hàng')}}</span>
                    </a>
                </li>
            @endif
        @endif
    @endif
    @if (MerchantCan::do('isApproved'))
        {{-- SUBSCRIPTIONS Menu --}}
        @if(MerchantCan::do('machine.hasAny'))
            @if (MerchantCan::do('subscription.list') || MerchantCan::do('subscription.history.list') || MerchantCan::do('ads.list'))
                <hr class="sidebar-divider">
                <div class="sidebar-heading">
                    {{__('Thuê bao')}}
                </div>
            @endif
            @if (MerchantCan::do('subscription.list'))
                <li class="nav-item {{ Nav::isRoute('merchant.subscription.list') }}">
                    <a class="nav-link" href="{{route('merchant.subscription.list')}}">
                        <i class="fas fa-calendar-check"></i>
                        <span>{{__('Thông tin thuê bao')}}</span>
                    </a>
                </li>
            @endif
            @if (MerchantCan::do('subscription.history.list'))
                <li class="nav-item {{ Nav::isRoute('merchant.subscription.history') }}">
                    <a class="nav-link" href="{{route('merchant.subscription.history')}}">
                        <i class="fas fa-history"></i>
                        <span>{{__('Lịch sử thuê bao')}}</span>
                    </a>
                </li>
            @endif
            @if (MerchantCan::do('ads.list'))
                <li class="nav-item {{ Nav::isRoute('merchant.ads.list') }}">
                    <a class="nav-link" href="{{route('merchant.ads.list')}}">
                        <i class="fas fa-history"></i>
                        <span>{{__('Quản trị quảng cáo')}}</span>
                    </a>
                </li>
            @endif
        @endif
    @endif
    @if (MerchantCan::do('isApproved'))
        @if(MerchantCan::do('machine.hasAny'))
            @if (MerchantCan::do('product.selling.list') || MerchantCan::do('product.list') || MerchantCan::do('product.sync.list'))
                {{-- PRODUCT Menu --}}
                <hr class="sidebar-divider">
                <div class="sidebar-heading">
                    {{__('Bán hàng')}}
                </div>
            @endif
            @if (MerchantCan::do('product.selling.list'))
                <li class="nav-item {{ Nav::isRoute('merchant.product.selling') }}">
                    <a class="nav-link" href="{{route('merchant.product.selling')}}">
                        <i class="fas fa-funnel-dollar"></i>
                        <span>{{__('Sản phẩm đang bán')}}</span>
                    </a>
                </li>
            @endif
            @if (MerchantCan::do('product.list'))
                <li class="nav-item {{ Nav::isRoute('merchant.product.list') }}">
                    <a class="nav-link" href="{{route('merchant.product.list')}}">
                        <i class="fas fa-luggage-cart"></i>
                        <span>{{__('Cài đặt danh sách hàng')}}</span>
                    </a>
                </li>
            @endif
            @if (MerchantCan::do('product.sync.list'))
                <li class="nav-item {{ Nav::isRoute('merchant.product.sync') }}">
                    <a class="nav-link" href="{{route('merchant.product.sync')}}">
                        <i class="fas fa-sync-alt"></i>
                        <span>{{__('Đồng bộ danh sách hàng')}}</span>
                    </a>
                </li>
        @endif
    @endif
@endif
<!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul><!-- End of Sidebar -->
