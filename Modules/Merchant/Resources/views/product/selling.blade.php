@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị danh sách sản phẩm đang bán') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm của bạn</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th style="width: 150px">Tên sản phẩm</th>
                                <th>Miêu tả ngắn</th>
                                <th class="text-center">Hình ảnh</th>
                                <th class="" style="width: 180px">Giá bán</th>
                                <th class="" style="width: 180px">Máy bán hàng</th>
                                <th class="" style="width: 150px">Số lượng (sản phẩm)</th>
                                <th class="">Ngày tạo</th>
                                @if(auth(MERCHANT)->user()->can('product.selling.edit'))
                                    <th class="text-center" style="width: 150px">Chức năng</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra-js')
    <script>
        $(function () {
            var table = $('#dataTable-vti').DataTable({
                processing: true,
                // serverSide: true,
                ajax: "{{ route('merchant.product.selling') }}",
                order: [[6, 'DESC']],
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'brief', name: 'brief'},
                    {data: 'image', name: 'image', orderable: false, searchable: false},
                    {data: 'price', name: 'price', orderable: false, },
                    {data: 'machines', name: 'machines', },
                    {data: 'count', name: 'count',},
                    {data: 'created_at', name: 'created_at'},
                        @if(auth(MERCHANT)->user()->can('product.selling.edit'))
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false
                    },
                    @endif
                ]
            });
        });
    </script>
@endsection
