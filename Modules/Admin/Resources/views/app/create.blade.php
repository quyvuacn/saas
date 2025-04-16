@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Các phiên bản máy bán hàng') }}</h1>

    <div class="alert alert-danger border-left-danger alert-dismissible fade show d-none" role="alert">
        <ul class="pl-4 my-2 box-error">
            <li></li>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <div class="row">

        <div class="col-lg-12">

            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Cập nhật phiên bản máy bán hàng</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{route('admin.app.store')}}" enctype="multipart/form-data" id="form_data">
                        @csrf
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Tên phiên bản</label>
                            <input type="text" id="version" name="version" class="form-control clear-invalid" placeholder="Nhập tên phiên bản" value="{{old('version')}}" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Version code</label>
                            <input type="number" id="code" name="code" class="form-control clear-invalid" placeholder="Nhập version code" value="{{old('code')}}" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Mô tả</label>
                            <textarea rows="4" id="brief" name="brief" class="form-control clear-invalid" placeholder="Nhập mô tả" value="{{old('brief')}}" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">File APK</label>
                            <input type="file" class="form-control" name="file" onchange="validateFile(this)">
                        </div>
                        <button type="submit" class="btn btn-facebook btn-block"><i class="fas fa-check"></i> Cập nhật phiên bản mới</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $(document).ready(function () {
            var flag = true;
            $('#form_data').submit(function () {
                if(!flag){
                    return false;
                }
                loading();
                var data = new FormData(this);
                axios.post($(this).attr('action'), data).then(response => {
                    hideLoading();
                    if (response.status == 200 && response.data.status) {
                        showMessageSuccess(response.data.message);
                        setTimeout(function () {
                            window.location.href = '/app';
                        }, 1500)
                    } else {
                        showMessageError();
                    }
                }).catch(e => {
                    hideLoading();
                    var response = e.response;
                    if(e.response.status == 422){
                        var errors = response.data.errors;
                        $('.alert-danger').removeClass('d-none');
                        $('.clear-invalid').removeClass('is-invalid');
                        contentErrors = '';
                        for (key in errors) {
                            $('#' + key).addClass('is-invalid');
                            contentErrors += '<li>'+errors[key]+'</li>';
                        }
                        $('.box-error').html(contentErrors);
                        return;
                    }
                    showMessageError();
                });
                return false
            })
        })

        function validateFile(e) {
            var file = $(e).val();
            var ext = file.substring(file.lastIndexOf('.') + 1).toLowerCase();
            if (e.files && e.files[0] && ext == 'apk')
            {
                return;
            }
            showMessageError('File APK không đúng định dạng');
            $(e).val('');
            flag = false;
        }
    </script>
@endsection
