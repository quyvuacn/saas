@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị thông báo - Thêm thông báo tới khách hàng của merchant') }}</h1>

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
                    <h6 class="m-0 font-weight-bold text-primary">Tạo thông báo mới</h6>
                </div>
                <form enctype="multipart/form-data" action="{{route('merchant.notify.store')}}" method="POST" id="form_data">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Tiêu đề của thông báo</label>
                            <input type="text" class="form-control {{$errors->has('name')?'is-invalid':''}}" name="name" id="name" placeholder="Nhập tiêu đề của thông báo" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Thời gian xuất bản thông báo</label>
                            <input type="text" class="form-control {{$errors->has('time_begin_show')?'is-invalid':''}}" name="time_begin_show" id="time_begin_show" value="{{ old('time_begin_show') }}" placeholder="dd/mm/yyyy" required>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlInput2">Thời gian ngừng hiển thị thông báo</label>
                            <input type="text" class="form-control {{$errors->has('time_end_show')?'is-invalid':''}}" value="{{old('time_end_show')}}" name="time_end_show" id="time_end_show" placeholder="dd/mm/yyyy" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Miêu tả ngắn</label>
                            <textarea rows="5" class="form-control {{$errors->has('brief')?'is-invalid':''}}" name="brief" value="{{old('brief')}}" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="name">Hình ảnh kèm thông báo</label>

                            <div class="mb-2">
                                <img width="300" src="" class="img-notify">
                            </div>

                            <div class="border p-2">
                                <input type="file" accept=".png, .jpg, .jpeg" name="file" onchange="validateImage(this)"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name">Nội dung chi tiết của thông báo</label>
                            <textarea rows="8" class="form-control {{$errors->has('content')?'is-invalid':''}}" name="content" value="{{old('content')}}" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-facebook btn-block"><i class="fas fa-check"></i> Thêm mới thông báo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('extra-js')
    <script>

        var flag = true;

        $(function () {
            flatpickr('#time_begin_show', {
                enableTime: true,
                locale: Vietnamese,
                dateFormat: "d/m/Y H:i",
                defaultDate: "{{old('date_added')}}"
            })
            flatpickr('#time_end_show', {
                enableTime: true,
                locale: Vietnamese,
                dateFormat: "d/m/Y H:i",
                defaultDate: "{{old('date_added')}}"
            })
        })

        $(document).ready(function () {
            $('#form_data').submit(function () {
                if(!flag){
                    return false;
                }
                var data = new FormData(this);
                axios.post($(this).attr('action'), data).then(response => {
                    if (response.status == 200 && response.data.status) {
                        showMessageSuccess(response.data.message);
                        setTimeout(function () {
                            window.location.href = '{{route('merchant.notify.list')}}';
                        }, 1500)
                    } else {
                        showMessageError();
                    }
                }).catch(e => {
                    var response = e.response;
                    if(e.response.status == 422){
                        var errors = response.data.errors;
                        $('.alert-danger').removeClass('d-none');
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

        function validateImage(e) {
            var image = $(e).val();
            var ext = image.substring(image.lastIndexOf('.') + 1).toLowerCase();

            var maxSize = 1024;

            if (e.files && e.files[0] && (ext == "png" || ext == "jpeg" || ext == "jpg"))
            {
                var sizeKb = e.files[0].size / 1024;
                if(sizeKb > maxSize){
                    showMessageError('Kích thước ảnh tối đa là 1Mb!');
                    flag = false;
                    $(e).val('');
                    return;
                }
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('.img-notify').attr('src', e.target.result);
                }
                reader.readAsDataURL(e.files[0]);
                return;
            }
            showMessageError('Ảnh phải có định dạng png, jpeg hoặc jpg');
            $(e).val('');
            flag = false;
        }
    </script>
@endsection

<style>
    .flatpickr-time{
        height: 40px !important;
    }
</style>
