@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị quảng cáo của bạn') }}</h1>

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
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sửa quảng cáo</h6>
                </div>
                <div class="card-body">
                    <ads-edit :machines="{{json_encode($machines)}}" :ads="{{json_encode($ads)}}" :ads_machines="{{json_encode($adsMachines)}}"></ads-edit>
                </div>
            </div>
        </div>
    </div>

@endsection

