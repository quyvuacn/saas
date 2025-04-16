@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Duyệt yêu cầu trả lại máy bán hàng') }}</h1>

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

        <div class="col-lg-6">

            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Nội dung yêu cầu</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Merchant: <strong>
                            @if (!empty($machineRequest->merchantInfo))
                                {{$machineRequest->merchantInfo->name}}
                            @endif
                            </strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Ngày trả máy: <strong>{{$machineRequest->date_return_machine ? $machineRequest->date_return_machine->format('d/m/Y') : '---'}}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Số điện thoại liên hệ: <strong class="text-primary">
                                @if(!empty($machineRequest->merchantInfo))
                                    {{$machineRequest->merchantInfo->phone}}
                                @endif
                            </strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Email liên hệ: <strong class="text-primary">
                                @if(!empty($machineRequest->merchantInfo))
                                    {{$machineRequest->merchantInfo->email}}
                                @endif
                            </strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Ngày tạo yêu cầu: <strong>{{date('H:i d/m/Y', strtotime($machineRequest->created_at))}}</strong>
                        </li>
                    </ul>
                </div>
            </div>

        </div>

        <div class="col-lg-6">

            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Duyệt yêu cầu</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{route('admin.machine.approveRequestBack', ['machineRequest' => $machineRequest->id])}}">
                        @csrf
                        <div class="form-group mt-4">
                            <label for="exampleFormControlTextarea1">Ngày VTI thu hồi máy</label>
                            <input class="form-control" type="text" placeholder="dd/mm/yyyy" name="date_receive" id="date_receive" value="{{old('date_receive')}}" required/>
                        </div>
                        <div class="form-group mt-4">
                            <label for="exampleFormControlTextarea1">Lý do merchant trả lại máy</label>
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="reason" required>{{old('reason')}}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-user btn-block">
                            Duyệt yêu cầu
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
            flatpickr('#date_receive', {
                locale: Vietnamese,
                dateFormat: "d/m/Y",
                defaultDate: "{{old('date_receive')}}"
            })
        })
    </script>
@endsection
