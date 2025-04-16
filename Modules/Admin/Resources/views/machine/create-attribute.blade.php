@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Khởi tạo thông số máy bán hàng') }}</h1>

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

    @include('admin::layouts.partials.header-message')

    <div class="row">

        <div class="col-lg-12">

            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <form method="POST" action="{{route('admin.machine.createAttributesPost')}}">
                        @csrf
                        <div class="form-group" id="box-attributes">
                            <label for="exampleFormControlInput1">Thông số máy bán hàng</label>
                            @foreach($attributes as $attribute)
                            <div class="row mb-3">
                                <input type="text" class="form-control col mr-2" placeholder="Nhập tên thông số" value="{{$attribute->attribute_name}}" readonly>
                                <input type="text" class="form-control col ml-2" placeholder="Nhập thông số mặc định" value="{{$attribute->value_default}}" readonly>
                            </div>
                            @endforeach
                        </div>
                        <div class="form-group">
                            <button onclick="return addAttributes();" class="btn btn-primary mb-2">Thêm thuộc tính</button>
                        </div>
                        <button type="submit" class="btn btn-facebook btn-block"><i class="fas fa-check"></i> Cập nhật thuộc tính</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        function addAttributes() {
            let htmlAppend = '<div class="row mb-3">\n' +
                '                                <input type="text" name="key[]" class="form-control col mr-2" placeholder="Nhập tên thông số" required>\n' +
                '                                <input type="text" name="value[]" class="form-control col ml-2" placeholder="Nhập thông số mặc định" required>\n' +
                '                            </div>';
            $('#box-attributes').append(htmlAppend);
            return false;
        }
    </script>
@endsection
