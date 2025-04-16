@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Sửa thông tin thuê bao') }}</h1>

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
                    <h6 class="m-0 font-weight-bold text-primary">Thay đổi thông tin thời gian thuê bao</h6>
                </div>
                <div class="card-body">
                    <form action="{{route('admin.subscription.update', ['subscription' => $subscription->id])}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Tên máy bán hàng</label>
                            <input type="text" class="form-control" id="exampleFormControlInput1" @if (!empty($subscription->machineSubscription)) value="{{$subscription->machineSubscription->name}}" @endif  readonly disabled="">
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput4">Tên merchant</label>
                            <input type="email" class="form-control" id="exampleFormControlInput4" @if (!empty($subscription->merchantSubscription)) value="{{$subscription->merchantSubscription->name}}" @endif readonly disabled="">
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput3">Ngày hết hạn</label>
                            <input name="date_expire" type="text" id="date_expire" class="form-control" value="{{date('d/m/Y', strtotime($subscription->date_expiration))}}">
                        </div>
                        <button type="submit" class="btn btn-facebook btn-block"><i class="fas fa-check"></i> Cập nhật thuê bao</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('extra-js')
    <script>
        $(function () {
            flatpickr('#date_expire', {
                locale: Vietnamese,
                dateFormat: "d/m/Y",
                defaultDate: "{{date('d/m/Y', strtotime($subscription->date_expiration))}}"
            })
        })
    </script>
@endsection
