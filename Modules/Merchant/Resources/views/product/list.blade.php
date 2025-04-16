@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị danh sách hàng hóa') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách sản phẩm của bạn</h6>
                </div>
                <div class="card-body">
                    @if(auth(MERCHANT)->user()->can('product.edit'))
                        <div class="row">
                            <div class="col-6">
                                <a href="{{route('merchant.product.create')}}">
                                    <button class="btn btn-primary mb-3">Thêm sản phẩm mới</button>
                                </a>
                            </div>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Miêu tả ngắn</th>
                                <th class="text-center">Hình ảnh</th>
                                <th class="text-center" style="width: 120px">Giá mặc định</th>
                                <th class="text-center">Ngày tạo</th>
                                @if(auth(MERCHANT)->user()->can('product.edit'))
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
                serverSide: true,
                ajax: "{{ route('merchant.product.list') }}",
                order: [[4, 'DESC']],
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'brief', name: 'brief'},
                    {data: 'image', name: 'image', orderable: false, searchable: false},
                    {data: 'price_default', name: 'price_default',},
                    {data: 'created_at', name: 'created_at'},
                        @if(auth(MERCHANT)->user()->can('product.edit'))
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false
                    },
                    @endif
                ]
            });
        });

        function deleteProduct(id) {
            Swal.fire({
                title: 'Bạn muốn xóa Sản phẩm này?',
                text: "Hành động này không thể khôi phục!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.value) {
                    axios.put('/product/delete/' + id).then(response => {
                        if (response.status == 200 && response.data.status) {
                            Swal.fire(
                                'Done!',
                                response.data.message,
                                'success'
                            );
                            $('#dataTable-vti').DataTable().ajax.reload();
                        } else {
                            Swal.fire(
                                'Opps',
                                response.data.message,
                                'error'
                            );
                        }
                    });
                }
            })
        }
    </script>
@endsection
