@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Sửa yêu cầu cung cấp máy bán hàng') }}</h1>

    @include('merchant::layouts.partials.header-error')

    <div class="row">
        <div class="col-lg-12">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sửa yêu cầu máy bán hàng</h6>
                </div>
                <div class="card-body">
                    <form action="{{route('merchant.machine.updateRequest', ['request'=> $request->id])}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="title">Nội dung yêu cầu</label>
                            <input type="text" class="form-control {{$errors->has('title')?'is-invalid':''}}" name="title" id="title" placeholder="Miêu tả vắn tắt yêu cầu của bạn" value="{{ old('title', $request->title) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="machine_request_count">Số máy bán hàng bạn cần</label>
                            <select class="form-control {{$errors->has('machine_request_count')?'is-invalid':''}}" name="machine_request_count" id="machine_request_count" required>
                                @for($i = 1 ; $i<=10 ; $i++)
                                    <option value="{{$i}}" {{(old('machine_request_count', $request->machine_request_count)==$i)?'selected':''}}>{{$i}}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="machine_date_receive">Ngày nhận máy</label>
                            <input type="text" class="form-control {{$errors->has('machine_date_receive')?'is-invalid':''}}" name="machine_date_receive" id="machine_date_receive" placeholder="DD/MM/YYYY" value="{{ old('machine_date_receive', date('d/m/Y', strtotime($request->machine_date_receive))) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="machine_position">Nơi đặt máy</label>
                            <input type="text" class="form-control {{$errors->has('machine_position')?'is-invalid':''}}" name="machine_position" id="machine_position" placeholder="Địa chỉ đặt/nhận máy bán hàng của bạn" value="{{ old('machine_position', $request->machine_position) }}" required>
                        </div>
                        <div class="form-group">
                            <label for="machine_other_request">Yêu cầu khác</label>
                            <textarea class="form-control {{$errors->has('machine_other_request')?'is-invalid':''}}" name="machine_other_request" id="machine_other_request" rows="3" required>{{ old('machine_other_request', $request->machine_other_request) }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-facebook btn-block">
                            <i class="fas fa-check"></i> {{__('Sửa yêu cầu')}}
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
            flatpickr('#machine_date_receive', {
                locale: Vietnamese,
                dateFormat: "d/m/Y",
                minDate: "today"
            })
        })
    </script>
@endsection

