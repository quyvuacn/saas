@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Đồng bộ danh sách hàng hóa') }}</h1>

    @include('merchant::layouts.partials.header-message')

    @php($totalPacks = $packsOfTrays->count())
    @php($activePacks = $packsOfTrays->where('status', 1)->count())

    <div class="row" id="sync-machine">
        <div class="col-lg-12">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin sản phẩm trên MÁY BÁN HÀNG
                        [ {{ucfirst($machine->name)}} ] <em>[ {{$machine->model}} ]</em></h6>
                </div>
                <form action="{{route('merchant.product.updateMachineProducts' ,['machine' => $machine->id])}}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if(isset($arrayTray) && $arrayTray->count() > 0)
                            <div class="row">
                                <div class="col-12 my-3">
                                    <div class="float-left">
                                        <button type="submit" class="btn btn-primary update-machine-product" {{$activePacks == 0 ? 'disabled' : ''}}>
                                            <em class="fas fa-check"></em>
                                            {{__('Cập nhật tất cả thay đổi với máy bán hàng')}}
                                        </button>
                                    </div>
                                    <div class="float-left ml-4 pt-2">
                                        <div class="form-check" {{$activePacks == 0 ? 'disabled' : ''}}>
                                            <input type="checkbox" class="form-check-input" id="exampleCheck1" name="sync_machine">
                                            <label class="form-check-label" for="exampleCheck1">
                                                Tự động cập nhật thông tin xuống máy bán hàng
                                                <strong>{{ucfirst($machine->name)}}</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <table class="table table-bordered table-striped" cellspacing="2">
                            <thead>
                            <tr>
                                <th style="vertical-align: middle; text-align: center; width: 15%;">Vị
                                    trí
                                </th>
                                <th style="vertical-align: middle; text-align: center;">Các sản phẩm
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($arrayTray) && $arrayTray->count() > 0)
                                @foreach($arrayTray as $tray)
                                    <tr>
                                        <td style="text-align: center; vertical-align: middle">
                                            <h5 class="mb-0"><strong>TRAY {{$tray}}</strong></h5>
                                        </td>
                                        <td class="text-center small">
                                            <ul class="list-group list-group-flush p-0 m-0">
                                                @foreach($packsOfTrays as $key => $pack)
                                                    @if($pack->tray_id == $tray)
                                                        <li class="list-group-item text-left">
                                                            <div class="d-flex">
                                                                <label class=""><br>
                                                                    <div>
                                                                        <span class="avatar avatar-md bg-danger pack-label">{{$pack->position_id}}</span>
                                                                    </div>
                                                                </label>
                                                                <label for="product_name_{{$tray}}_{{$pack->id}}" class="pl-3 product-name-label" data-pos="{{$tray}}_{{$pack->id}}">Sản phẩm:
                                                                    <select name="product_select_[{{$pack->id}}][{{$tray}}]" id="product_select_{{$tray}}_{{$pack->id}}" data-pos="{{$tray}}_{{$pack->id}}" class="form-control select-product-in-list" {{$pack->status == 1 ? '' : 'disabled'}}>
                                                                        <option value="">Chọn sản phẩm</option>
                                                                        @foreach($merchantProducts as $product)
                                                                            <option value="{{$product->id}}" @if($pack->product_id == $product->id) selected @endif>{{$product->name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                </label>
                                                                <label for="product_price_{{$tray}}_{{$pack->id}}" class="pl-2">Giá (Coin):
                                                                    <input type="number" name="product_price_[{{$pack->id}}][{{$tray}}]" id="product_price_{{$tray}}_{{$pack->id}}" min="0" max="100000"
                                                                        class="form-control" style="color:red"
                                                                        value="{{$pack->product_price && $pack->product_price > 0 ? $pack->product_price : ($pack->product && $pack->product->price_default > 0 ? $pack->product->price_default : 0)}}" {{$pack->status == 1 ? '' : 'disabled'}}>
                                                                </label>
                                                                <label for="product_qty_{{$tray}}_{{$pack->id}}" class="pl-2">Số lượng:
                                                                    <select name="product_qty_[{{$pack->id}}][{{$tray}}]" id="product_qty_{{$tray}}_{{$pack->id}}" class="form-control" {{$pack->status == 1 ? '' : 'disabled'}}>
                                                                        @for($i = 0; $i <= $pack->product_item_number; $i++)
                                                                            <option value="{{$i}}" @if($i == $pack->product_item_current) selected @endif>{{$i}}</option>
                                                                        @endfor
                                                                    </select>
                                                                </label>
                                                                <label class="pl-3">Trạng thái ( Click để thay đổi )
                                                                    <div>
                                                                        <button class="btn btn-{{$pack->status == 1 ? 'success' : 'secondary'}} btn-md pack-toggle-btn" style="height: 40px" data-pack="{{$pack->id}}" data-status="{{$pack->status}}" data-tray="{{$tray}}">
                                                                            <i class="fas fa-check"></i>
                                                                            <span>{{$pack->status == 1 ? 'Activated' : 'Disabled'}}</span>
                                                                        </button>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">
                                        <div class="text-center">Máy bán hàng chưa được cấu hình! Liên hệ Admin để biết thêm chi tiết</div>
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('extra-js')
    <script>
        var products = {!! $merchantProductsJS !!}
        $(function () {
            $('.select-product-in-list').select2({
                language: "vi",
                // placeholder: 'This is my placeholder',
                allowClear: true
            })
            $('body').delegate('.pack-toggle-btn', 'click', function (e) {
                e.preventDefault();
                var pack = $(this).attr('data-pack');
                var status = $(this).attr('data-status');
                var tray = $(this).attr('data-tray');
                var action = status == 0 ? 'Active' : 'Disable';
                Swal.fire({
                    title: 'Bạn muốn ' + action + ' Pack này?',
                    text: "",
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: action,
                    cancelButtonText: 'Hủy',
                }).then((result) => {
                    if (result.value) {
                        axios.post('/product/' + pack + '/toggle-pack', {}).then(response => {
                            if (response.status == 200 && response.data.status) {
                                Swal.fire(
                                    action + ' Pack thành công!',
                                    '',
                                    'success'
                                );
                                var text = status == 0 ? 'Activated' : 'Disabled';
                                if (status == 0) {
                                    $(this).removeClass('btn-secondary');
                                    $(this).addClass('btn-success');
                                    $(this).attr('data-status', 1);
                                    $(this).find('span').text(text);

                                    $('#product_select_' + tray + '_' + pack).prop('disabled', false);
                                    $('#product_price_' + tray + '_' + pack).prop('disabled', false);
                                    $('#product_qty_' + tray + '_' + pack).prop('disabled', false);
                                } else {
                                    $(this).removeClass('btn-success');
                                    $(this).addClass('btn-secondary');
                                    $(this).attr('data-status', 0);
                                    $(this).find('span').text(text);

                                    $('#product_select_' + tray + '_' + pack).prop('disabled', true);
                                    $('#product_price_' + tray + '_' + pack).prop('disabled', true);
                                    $('#product_qty_' + tray + '_' + pack).prop('disabled', true);
                                }

                            } else {
                                Swal.fire(
                                    action + ' Pack không thành công!',
                                    response.data.message,
                                    'error'
                                );
                            }

                            var disabled_count = 0;
                            // CHECK has any ACTIVE PACK
                            $('.pack-toggle-btn').each(function (index, value) {
                                if ($(value).attr('data-status') == 0) {
                                    disabled_count++;
                                }
                            });

                            if (disabled_count == '{{$totalPacks}}') {
                                $('.update-machine-product').prop('disabled', true);
                            } else {
                                $('.update-machine-product').prop('disabled', false);
                            }
                        });
                    }
                })
            });
            // $('body').delegate('select[name^="product_select_"]', 'click', function (e) {
            //     e.preventDefault();
            //     let product_id = $(this).val();
            //     let pos = $(this).attr('data-pos')
            //     if (product_id && products[product_id]) {
            //         $('#product_price_' + pos).val(products[product_id])
            //     } else {
            //         $('#product_price_' + pos).val(0)
            //     }
            // });

            $('.select-product-in-list').on('select2:select', function (e) {
                var data = e.params.data;
                let product_id = data.id;
                let pos = $(this).closest('.product-name-label').attr('data-pos')
                if (product_id && products[product_id]) {
                    $('#product_price_' + pos).val(products[product_id])
                } else {
                    $('#product_price_' + pos).val(0)
                }
            });
        })
    </script>
@endsection
