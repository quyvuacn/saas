<?php
use Modules\Admin\Classes\Facades\AdminCan;
?>
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{route('admin.dashboard')}}">
        <div class="sidebar-brand-icon">
            <img src="/img/logo1.png" width="30" height="40"/>
        </div>
        <div class="sidebar-brand-text mx-3">ADMIN <sup>v1.0</sup></div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Nav::isRoute('admin.dashboard') }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <i class="fas fa-fw fa-th-large"></i>
            <span>{{ __('Dashboard') }}</span></a>
    </li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        {{__('Tài khoản & Phân quyền')}}
    </div>
    <!-- ACCOUNT Menu -->
    @if (AdminCan::do('adm.account.list'))
        <li class="nav-item {{ Nav::isRoute('admin.account.list') }}">
            <a class="nav-link" href="{{route('admin.account.list')}}">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>{{__('Danh sách tài khoản')}}</span>
            </a>
        </li>
    @endif
    @if (AdminCan::do('adm.account.edit'))
    <li class="nav-item {{ Nav::isRoute('admin.account.permission') }}">
        <a class="nav-link" href="{{route('admin.account.permission')}}">
            <i class="fas fa-fingerprint"></i>
            <span>{{__('Phân quyền')}}</span>
        </a>
    </li>
    @endif


    @if (AdminCan::do('adm.machine.app_version_list'))
        <hr class="sidebar-divider">
        <li class="nav-item {{ Nav::isRoute('adm.machine.app_version_list') }}">
            <a class="nav-link" href="{{route('admin.app.list')}}">
                <i class="fa fa-cog"></i>
                <span>{{__('Quản lý version máy bán hàng')}}</span>
            </a>
        </li>
    @endif
    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        {{__('Quản lý Merchant')}}
    </div>
    <!-- MERCHANT Menu -->

    @if (AdminCan::do('adm.merchant_request.list'))
    <li class="nav-item {{ Nav::isRoute('admin.merchant.request') }}">
        <a class="nav-link" href="{{route('admin.merchant.request')}}">
            <i class="fas fa-user-plus"></i>
            <span>{{__('Danh sách yêu cầu')}}</span>
        </a>
    </li>
    @endif

    @if (AdminCan::do('adm.merchant.list'))
    <li class="nav-item {{ Nav::isRoute('admin.merchant.list') }}">
        <a class="nav-link" href="{{route('admin.merchant.list')}}">
            <i class="fas fa-user-friends"></i>
            <span>{{__('Danh sách Merchant')}}</span>
        </a>
    </li>
    @endif
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        {{__('Máy bán hàng')}}
    </div>
    <!-- MACHINE Menu -->
    @if (AdminCan::do('adm.machine_request.list'))
    <li class="nav-item {{ Nav::isRoute('admin.machine.request') }}">
        <a class="nav-link" href="{{route('admin.machine.request')}}">
            <i class="fas fa-file-signature"></i>
            <span>{{__('Yêu cầu máy bán hàng')}}</span>
        </a>
    </li>
    @endif

    @if (AdminCan::do('adm.machine_request_back.list'))
    <li class="nav-item {{ Nav::isRoute('admin.machine.requestBack') }}">
        <a class="nav-link" href="{{route('admin.machine.requestBack')}}">
            <i class="fas fa-backspace"></i>
            <span>{{__('Yêu cầu trả máy bán hàng')}}</span>
        </a>
    </li>
    @endif

    @if (AdminCan::do('adm.machine.list'))
    <li class="nav-item {{ Nav::isRoute('admin.machine.list') }}">
        <a class="nav-link" href="{{route('admin.machine.list')}}">
            <i class="fas fa-store"></i>
            <span>{{__('Danh sách máy bán hàng')}}</span>
        </a>
    </li>
    @endif
    @if (AdminCan::do('adm.machine.processing'))
    <li class="nav-item {{ Nav::isRoute('admin.machine.requestProcessing') }}">
        <a class="nav-link" href="{{route('admin.machine.requestProcessing')}}">
            <i class="fas fa-tasks"></i>
            <span>{{__('Các yêu cầu đang xử lý')}}</span>
        </a>
    </li>
    @endif
    <hr class="sidebar-divider">
    <div class="sidebar-heading">
        {{__('Quản lý thuê bao')}}
    </div>
    <!-- SUBSCRIPTIONS Menu -->
    @if (AdminCan::do('adm.subscription_request.list'))
    <li class="nav-item {{ Nav::isRoute('admin.subscription.extend') }}">
        <a class="nav-link" href="{{route('admin.subscription.extend')}}">
            <i class="fas fa-calendar-check"></i>
            <span>{{__('Yêu cầu gia hạn thuê bao')}}</span>
        </a>
    </li>
    @endif

    @if (AdminCan::do('adm.subscription.list'))
    <li class="nav-item {{ Nav::isRoute('admin.subscription.list') }}">
        <a class="nav-link" href="{{route('admin.subscription.list')}}">
            <i class="fas fa-id-card"></i>
            <span>{{__('Danh sách thuê bao')}}</span>
        </a>
    </li>
    @endif
    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul><!-- End of Sidebar -->
