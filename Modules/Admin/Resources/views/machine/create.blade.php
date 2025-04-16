@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Thêm thông tin máy bán hàng mới') }}</h1>

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
                    <h6 class="m-0 font-weight-bold text-primary">Thêm các máy bán hàng mới nhập về</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{route('admin.machine.createPost')}}">
                        @csrf
                        <div class="form-group">
                            <label for="exampleFormControlInput1">Tên máy bán hàng</label>
                            <input type="text" name="name" class="form-control" placeholder="Nhập tên của máy" value="{{old('name')}}" required>
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlInput4">Model của máy</label>
                            <input type="text" name="model" class="form-control" placeholder="Nhập model của máy" value="{{old('model')}}" required>
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlInput3">Số lượng máy nhập về</label>
                            <input type="number" name="machine_count" class="form-control" placeholder="Nhập số máy nhập về có cùng model.." value="{{old('machine_count')}}">
                        </div>

                        <div class="form-group">
                            <label for="exampleFormControlInput3">Chọn merchant</label>
                            <select class="form-control" name="merchant">
                                <option value="0">Chọn merchant</option>
                                @foreach($merchants as $merchant)
                                    <option value="{{$merchant['id']}}">{{$merchant['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group more-info-subscription" style="margin-left:20px;display: none">
                            <div class="form-group">
                                <label for="exampleFormControlInput2">Ngày nhận máy</label>
                                <input type="text" class="form-control" value="{{old('date_added')}}" name="date_added" id="date_added" placeholder="dd/mm/yyyy" required>
                            </div>
                            <div class="form-group">
                                <label for="exampleFormControlInput3">Số tháng gia hạn</label>
                                <input type="number" name="month_subscription" class="form-control" placeholder="Nhập số tháng gia hạn máy" value="{{old('month_subscription')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleFormControlTextarea1">Thông tin cấu hình máy</label>
                            <div class="border flex-column row block-attr">
                                @if(!empty(old('attribute_name')) && !empty(old('attribute_value')))
                                @foreach(old('attribute_name') as $k => $v)
                                    @if (!empty(old('attribute_value')[$k]))
                                        <div class="box-attribute">
                                            <select name="attribute_name[]" class="form-control col-6 d-inline-block">
                                                <option value="0">Chọn thuộc tính</option>
                                                @foreach($listAttribute as $attr)
                                                    <option value="{{$attr['id']}}" @if ($attr['id'] == $v) selected="selected" @endif>{{$attr['attribute_name']}}</option>
                                                @endforeach
                                            </select>
                                            <input name="attribute_value[]" placeholder="Giá trị thuộc tính" value="{{old('attribute_value')[$k]}}" class="form-control col-5 d-inline-block">
                                            <span onclick="return closeAttribute(this);" class="btn"><i class="fas fa-trash fa-6 text-danger"></i></span>
                                        </div>
                                    @endif
                                @endforeach
                                @else
                                <div class="box-attribute">
                                    <select name="attribute_name[]" class="form-control col-6 d-inline-block" readonly>
                                        <option value="0">Chọn thuộc tính</option>
                                    </select>
                                    <input name="attribute_value[]" placeholder="Giá trị thuộc tính.." class="form-control col-5 d-inline-block" readonly>
                                    <span onclick="return closeAttribute(this);" class="btn"><i class="fas fa-trash fa-6 text-danger"></i></span>
                                </div>
                                @endif
                            </div>
                            <button type="button" onclick="return addAttribute();" class="btn-add-attribute btn btn-primary mt-3">
                                Add Attribute
                            </button>
                        </div>

                        <div class="form-group">
                            <label>Thông tin Tray/Pack của máy</label>
                            <div class="border box-tray">
                                <select name="tray_count" class="form-control">
                                    <option value="0">Chọn số lượng tray</option>
                                    @for ($i = 1; $i <= 10; $i++)
                                        <option value="{{$i}}">{{$i}} Tray</option>
                                    @endfor
                                </select>
                                <div class="box-pack">

                                </div>
                            </div>
                        </div>

                        <div class="form-group create-status-machine">
                            <label>Trạng thái máy</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status_machine" id="exampleRadios3" value="0" checked>
                                <label class="form-check-label mr-5" for="exampleRadios3">
                                    Sẵn trong kho
                                </label>

                                <input class="form-check-input" type="radio" name="status_machine" id="exampleRadios1" value="1">
                                <label class="form-check-label" for="exampleRadios1">
                                    Vấn đề khác
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-facebook btn-block"><i class="fas fa-check"></i> Thêm máy bán hàng</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
    .box-attribute{
        height: 40px;
        margin: 10px 0 5px 10px;
    }
    .block-attr{
        min-height: 60px;
        padding: 0 0 5px;
    }
    .box-tray{
        min-height: 60px;
        padding: 0 0 5px;
    }
    .box-pack > div:nth-child(odd){
        background: #a3cce8;
    }
    .box-pack > div:nth-child(even){
        background: #c9dae8;
    }
    .box-tray select {
        margin: 10px;
        width: calc(100% - 30px);
    }
    .box-pack input{
        margin: 3px 10px;
        width: 100px;
    }
    .max-product-pack{
        margin-bottom: 20px;
    }
    .box-sum-pack{
        padding: 5px 0 5px 30px;
        border-bottom: 1px solid #ccc;
        margin: 5px 0;
    }
    .sum-pack select{
        width: auto;
    }
</style>

@section('extra-js')
    <script>
        var listAttr = {!! $listAttribute !!};
        var flag = @if(!empty(old('attribute_name'))) true @else false @endif

        var optionSumPack = '';

        $(function () {
            flatpickr('#date_added', {
                locale: Vietnamese,
                dateFormat: "d/m/Y",
                defaultDate: "{{old('date_added')}}"
            })
        })

        $(document).ready(function () {
            addOptionAttribute();

            for(let j = 1; j <= 10; j++){
                var checked = j == 10 ? 'selected' : '';
                optionSumPack += '<option value="'+j+'" '+checked+'>'+j+'</option>';
            }

            $('select[name=merchant]').on('change', function(){
                if($(this).find(":selected").val() != 0){
                    $('.more-info-subscription').slideDown();
                    $('.create-status-machine').slideUp();
                } else {
                    $('.more-info-subscription').slideUp();
                    $('.create-status-machine').slideDown();
                }
            })

            $('select[name=tray_count]').on('change', function () {
                let val = $(this).find(':selected').val();
                let countPackCurrent = $('.box-sum-pack').length;
                var content = '';
                if(val > countPackCurrent){
                    for(let i = countPackCurrent + 1; i <= val; i++){
                        content += '<div class="box-sum-pack">' +
                            '<label class="mb-0">Số lượng Pack của Tray '+i+'</label>' +
                            '<select class="form-control ml-0" class="pack-count" name="pack_count[]" data-tray="'+i+'">';
                        for (let j = 0; j <= 10; j++){
                            content += '<option value="'+j+'">'+j+' Pack</option>';
                        }
                        content += '</select>';
                        content += '<div class="max-product-pack"></div>';
                        content += '</div>';
                    }
                } else {
                    var vRemove = parseInt(val) + 1;
                    $('.box-sum-pack:nth-child(n + '+vRemove+')').remove();
                }
                $('.box-pack').append(content);
            })
        })

        function addPack() {
            var tray = $('input[name=tray_count]').val();
            if(isNaN(tray) || tray < 0 || tray > 20){
                showMessageError('Số lượng tray phải là số lớn hơn 1 và nhỏ hơn 20');
            }
        }

        function addOptionAttribute() {
            if(flag)
                return;
            var content = '';
            listAttr.forEach(v => {
                content += '<option value="'+v['id']+'">'+v['attribute_name']+'</option>';
            })

            $('.block-attr select').each(function () {
                $(this).append(content);
            })
        }

        function addAttribute() {

            var option = '';
            listAttr.forEach(v => {
                option += '<option value="'+v['id']+'">'+v['attribute_name']+'</option>';
            })

            let content = '<div class="box-attribute">\n' +
                '               <select name="attribute_name[]" class="form-control col-6 d-inline-block" readonly>\n' +
                '               <option value="0">Chọn thuộc tính</option>' +
                                option +
                '               </select>\n' +
                '               <input name="attribute_value[]" placeholder="Giá trị thuộc tính" class="form-control col-5 d-inline-block" readonly>\n' +
                '               <span onclick="return closeAttribute(this);" class="btn"><i class="fas fa-trash fa-6 text-danger"></i></span>\n' +
                '           </div>';
            $('.block-attr').append(content);
        }
        function closeAttribute(e) {
            e.closest('div').remove();
            return false;
        }

        $(document).off('change').on('change', '.box-attribute select', function(e) {
            var v = $(this).find(':selected').val();
            if(v != 0) {
                $(this).removeAttr('readonly');
                $(this).closest('div').children('input').removeAttr('readonly').val(listAttr[v]['value_default']);
            } else {
                $(this).attr('readonly', 'readonly');
                $(this).closest('div').children('input').val("").attr('readonly', 'readonly');
            }
        }).on('change', '.box-sum-pack select', function(e) {
            let val = $(this).find(':selected').val();
            let trayId = $(this).data('tray');
            let countProductPack = $(this).closest('div.box-sum-pack').children('.max-product-pack').children('div').length;
            var content = '';
            if(val > countProductPack){
                for(let i = countProductPack + 1; i <= val; i++){
                    content += '<div class="d-inline-block sum-pack">' +
                        '<label class="d-inline-block">Pack '+i+': </label>' +
                        '<select class="form-control d-inline-block" type="number" name="max_product_pack['+trayId+'][]">' +
                        optionSumPack +
                        '</select>' +
                        '</div>';
                }
            } else {
                var vRemove = parseInt(val) + 1;
                $(this).closest('div').children('.max-product-pack').children('div:nth-child(n + '+vRemove+')').remove();
            }
            $(this).closest('div').children('div.max-product-pack').append(content);
        })
    </script>
@endsection
