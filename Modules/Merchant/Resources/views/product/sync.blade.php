@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Đồng bộ danh sách hàng hóa') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách máy bán hàng</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th style="vertical-align: middle; text-align: center; width: 15%;">Mã
                                    máy/Model
                                </th>
                                <th style="vertical-align: middle; text-align: center;">Thông
                                    số sản phẩm
                                </th>
                                <th class="text-center" style="width: 15%">Ngày tạo</th>
                                @if(auth(MERCHANT)->user()->can('product.sync.edit'))
                                    <th style="vertical-align: middle; text-align: center; width: 15%">Chức
                                        năng
                                    </th>
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
                serverSide: true,
                ajax: "{{ route('merchant.product.sync') }}",
                order: [[2, 'DESC']],
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'attribute', name: 'attribute'},
                    {data: 'start_date', name: 'start_date'},
                        @if(auth(MERCHANT)->user()->can('product.sync.edit'))
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false
                    },
                    @endif
                ]
            });
        });
    </script>
@endsection
