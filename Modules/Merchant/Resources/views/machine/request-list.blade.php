@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Các yêu cầu máy bán hàng của bạn') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row" id="mc-request-list">
        @if(isset($machineRequests) && $machineRequests)
            @foreach($machineRequests as $request)
                <div class="col-lg-6 mb-3">
                    <ul class="list-group">
                        <li class="list-group-item active d-flex justify-content-between align-items-center">
                            {{$request->title??'---'}}
                            @if (auth(MERCHANT)->user()->can('machine_request.edit'))
                                <a href="{{route('merchant.machine.editRequest', ['request' => $request->id])}}" style="color: white" class="badge badge-primary badge-pill">[ Sửa yêu cầu ]</a>
                            @endif
                        </li>
                        <li class="list-group-item">Số lượng máy: {{$request->machine_request_count ?? 0}}</li>
                        <li class="list-group-item">Ngày yêu cầu: {{$request->created_at->format('d/m/Y')}}</li>
                        <li class="list-group-item">Ngày nhận máy: {{$request->machine_date_receive->format('d/m/Y')}}</li>
                        <li class="list-group-item">Nơi giao máy: {{$request->machine_position ?? '---'}}
                        </li>
                        <li class="list-group-item">Yêu cầu khác: {{$request->machine_other_request ?? '---'}}</li>
                        <li class="list-group-item">Tình trạng yêu cầu:
                            <span class="badge badge-info badge-pill">Tạo mới</span></li>
                        @if (auth(MERCHANT)->user()->can('machine_request.edit'))
                            <li class="list-group-item">
                                <a href="javascript:void(0)" onclick="event.preventDefault(); document.getElementById('delete-request-form-{{$request->id}}').submit();">
                                    <i class="fas fa-trash"></i>
                                    <span class="text-danger">{{__('Xóa yêu cầu')}}</span>
                                    <form id="delete-request-form-{{$request->id}}" action="{{ route('merchant.machine.deleteRequest', ['request'=>$request->id]) }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endforeach
        @endif
        @if($machineRequests->count() < 6 && auth(MERCHANT)->user()->can('machine_request.list'))
            <div class="col-lg-6 mb-3 add-request">
                <a href="{{route('merchant.machine.request')}}">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <span class="icon" style="font-size: 80px">
                                <i class="fas fa-plus"></i>
                            </span>
                            <p style="font-size: 30px">
                                Thêm yêu cầu mới
                            </p>
                        </div>
                    </div>
                </a>
            </div>
        @endif
    </div>
@endsection

