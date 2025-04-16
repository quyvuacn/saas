<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('Máy bán hàng tự động VTI-IVM | VTI Joint Stock Company') }}</title>

    <!-- Fonts -->
    <link href="{{ asset('vendor/fontawesome/css/all.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Styles -->
{{--    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">--}}
{{--    <link rel="stylesheet" href="{{asset('css/app.css')}}">--}}
    <link href="https://1giay.vn/images/favicon.png" rel="icon" type="image/png">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/main.css') }}">
    <link href="{{ asset('css/special.css') }}" rel="stylesheet">

    <!-- Favicon -->
    <link href="https://1giay.vn/images/favicon.png" rel="icon" type="image/png">
</head>
<body>

@yield('main-content')

<!-- Scripts -->
<script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('vendor/bootstrap/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
@yield('extra-js')
</body>
</html>
