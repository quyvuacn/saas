@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Tạo yêu cầu nạp coin') }}</h1>

    @include('merchant::layouts.partials.header-message')

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
    <merchant-component :users="{{json_encode($users)}}" :coins="'{{json_encode(config('merchant.coin_recharge'))}}'" :min_credit_quote="'{{json_encode(config('merchant.min_credit_quote'))}}'" :max_credit_quote="'{{json_encode(config('merchant.max_credit_quote'))}}'"></merchant-component>
@endsection
