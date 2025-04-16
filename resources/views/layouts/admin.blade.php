<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Máy bán hàng tự động VTI-IVM | VTI Joint Stock Company">
    <meta name="author" content="VTI">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{--<title>{{ config('app.name', 'Máy bán hàng tự động VTI-Scan | VTI Joint Stock Company') }}</title>--}}
    <title>{{__('Máy bán hàng tự động VTI-IVM | VTI Joint Stock Company')}}</title>
    <!-- Fonts -->
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css?family=Roboto:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    @yield('extra-css')
    <!-- Custom styles for this page -->
    <link href="{{asset('vendor/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet">
    <!-- Favicon -->
    <link href="https://1giay.vn/images/favicon.png" rel="icon" type="image/png">
    <link rel="apple-touch-icon-precomposed" href="{{asset('img/favicon.png')}}">

    <link rel="apple-touch-icon" sizes="57x57" href="{{asset('img/favicon.png')}}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{asset('img/favicon.png')}}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{asset('img/favicon.png')}}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{asset('img/favicon.png')}}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{asset('img/favicon.png')}}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{asset('img/favicon.png')}}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{asset('img/favicon.png')}}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{asset('img/favicon.png')}}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('img/favicon.png')}}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{asset('img/favicon.png')}}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('img/favicon.png')}}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{asset('img/favicon.png')}}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('img/favicon.png')}}">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#1c6a99">


</head>
<body id="page-top">
<!-- Page Wrapper -->
<div id="app">
    <div id="wrapper">
        @yield('sidebar')
        {{--@include('layouts.partials.sidebar')--}}
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
            @yield('top-sidebar')
            <!-- Begin Page Content -->
                <div class="container-fluid">
                    @yield('main-content')
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Main Content -->
            @include('layouts.partials.footer')
        </div>
        <!-- End of Content Wrapper -->
            <!-- Scroll to Top Button-->
        <a class="scroll-to-top rounded" href="#page-top">
            <i class="fas fa-angle-up"></i>
        </a>
        <div class="modal fade bd-example-modal-lg" id="modal-spinner"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg position-fixed" role="document" style="top: 0;left: 0;bottom: 0;right: 0;height: 50px;margin: auto;">
                <div class="modal-content d-block bg-transparent border-0" style="width: 48px;margin: auto">
                    <span class="fa fa-spinner fa-spin fa-3x text-white"></span>
                </div>
            </div>
        </div>
        @yield('logout-modal')
    </div>
</div>
<!-- Scripts -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
{{--Datatable--}}
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('js/demo/datatables-demo.js') }}"></script>
<script src="{{ asset('js/common.js') }}"></script>
@yield('extra-js')
</body>
</html>
