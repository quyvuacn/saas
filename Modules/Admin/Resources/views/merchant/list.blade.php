@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Danh sách các tài khoản merchant') }}</h1>

    @include('admin::layouts.partials.header-message')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Các merchant đang hoạt động</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>Tên tài khoản <br>Tên công ty/cá nhân</th>
                        <th>Email<br>Số điện thoại</th>
                        <th>Số máy bán hàng</th>
                        <th class="text-center">Ngày bắt đầu</th>
                        <th>Địa chỉ chính</th>
                        <th>Chức năng</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($merchants as $merchant)
                        <tr>
                            <td><a href="{{route('admin.merchant.edit', ['merchantId' => $merchant->id])}}">{{$merchant->name}}</a> <br> @if (!empty($merchant->merchantInfo)) {{$merchant->merchantInfo->merchant_company}} @endif</td>
                            <td>{{$merchant->email}} <br> {{$merchant->phone}}</td>
                            <td class="text-center" data-sort="{{$merchant->machine_count}}">{{$merchant->machine_count}}<br>
                                <a href="{{route('admin.machine.list', ['merchant_id' => $merchant->id])}}" class="small">[Xem chi tiết]</a>
                            </td>
                            <td class="text-center">@if (!empty($merchant->merchantInfo)) {{date('d/m/Y', strtotime($merchant->merchantInfo->merchant_active_date))}} @endif</td>
                            <td>
                                @if ($merchant->parent_id == 0)
                                    @if (!empty($merchant->merchantInfo))
                                        {{$merchant->merchantInfo->merchant_address}}
                                    @endif
                                @else
                                    @php $merchantParent = $merchant->commonMerchant() @endphp
                                    @if(!empty($merchantParent->merchantInfo))
                                        {{$merchantParent->merchantInfo->merchant_address}}
                                    @endif
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{route('admin.merchant.edit', ['merchantId' => $merchant->id])}}" class="btn btn-primary">
                                    Sửa
                                </a>
                                <button onclick="confirmDeleteMerchant({{$merchant->id . ',' . $merchant->machine_count}})" class="btn btn-danger">
                                    Xóa
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Yêu cầu xóa Merchant không
                                    thành công!</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <h5 class="alert alert-danger text-center">
                                    Bạn không được phép xóa Merchant đang có máy bán hàng hoạt động.
                                </h5>
                                <p>
                                    Vui lòng hoàn thiện <strong>thủ tục thu hồi máy bán hàng</strong> của
                                    Merchant trước khi xóa tài
                                    khoản Merchant.
                                </p>
                                <ul class="list-group">
                                    <li class="list-group-item">
                                        <a href="" class="show-info-subscription">
                                            <em class="fa fa-sm fa-chevron-right"></em>
                                            Xem thông tin thuê bao của Merchant
                                        </a>
                                    </li>
                                    <li class="list-group-item">
                                        <a href="" class="show-list-machine">
                                            <em class="fa fa-sm fa-chevron-right"></em>
                                            Xem danh sách các máy bán hàng của Merchant
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="exampleModal1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel1">Bạn có chắc chắn muốn xóa
                                    Merchant hay không?</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>
                                    Hãy đảm bảo rằng hợp đồng đã ký với Merchant đã được thanh lý trước khi xóa tài khoản của Merchant.
                                </p>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                                    <label class="form-check-label" for="defaultCheck1">
                                        Các nghĩa vụ trong hợp đồng với Merchant đã được thực hiện hết
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="defaultCheck2">
                                    <label class="form-check-label" for="defaultCheck2">
                                        Đã ký thanh lý hợp đồng (hoặc tự động thanh lý hợp đồng)
                                    </label>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger btn-confirm-delete" data-id="">
                                    Xác nhận xóa Merchant
                                </button>
                                <button type="button" class="btn btn-secondary btn-close-modal" data-dismiss="modal">
                                    Đóng
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="d-none show-notify-error" data-toggle="modal" data-target="#exampleModal"></button>
                <button class="d-none show-notify-confirm" data-toggle="modal" data-target="#exampleModal1"></button>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $(document).ready(function() {
            $('#dataTable1').DataTable({
                "order": [[ 3, "desc" ]],
                "columnDefs": [ {
                    "targets": 5,
                    "orderable": false,
                    "searchable": false
                } ]
            });
        });
        function confirmDeleteMerchant(merchantId, machineCount) {
            if(machineCount > 0){
                $('.show-notify-error').trigger('click');
                $('.show-info-subscription').attr('href', '/subscription/history/' + merchantId);
                $('.show-list-machine').attr('href', '/machine?merchant_id=' + merchantId);
                return;
            }
            $('.btn-confirm-delete').data('id', merchantId);
            $('.show-notify-confirm').trigger('click');
            $('#defaultCheck1').parent('div').removeClass('text-danger');
            $('#defaultCheck2').parent('div').removeClass('text-danger');
            $('#defaultCheck1').prop('checked', false);
            $('#defaultCheck2').prop('checked', false);
        }
        $('.btn-confirm-delete').on('click', function () {
            let id = $(this).data('id');
            let url = '/merchant/delete/' + id;
            let flag = true;
            if(!$('#defaultCheck1').is(':checked')){
                $('#defaultCheck1').parent('div').addClass('text-danger');
                flag = false;
            }
            if(!$('#defaultCheck2').is(':checked')){
                $('#defaultCheck2').parent('div').addClass('text-danger');
                flag = false;
            }
            if(!flag){
                return;
            }
            $.ajax({
                url: url,
                type: 'PUT',
                headers:{
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    data = JSON.parse(data)
                    if(data.status == 1) {
                        showMessageSuccess()
                        setTimeout(function () {
                            location.reload();
                        }, 1500)
                    } else {
                        $('.show-notify-error').trigger('click');
                    }
                },
                fail: function(xhr, textStatus, errorThrown){
                    showMessageError();
                }
            });
            $('.btn-close-modal').trigger('click');
        })
    </script>
@endsection
