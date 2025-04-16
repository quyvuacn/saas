@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị quảng cáo của bạn') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách quảng cáo trên máy bán hàng của bạn</h6>
                </div>
                <div class="card-body">
                    @if(auth(MERCHANT)->user()->can('ads.edit'))
                        <div class="row">
                            <div class="col-6">
                                <a href="{{route('merchant.ads.create')}}">
                                    <button class="btn btn-primary mb-3">Thêm quảng cáo</button>
                                </a>
                                <a href="javascript:void(0)" class="btn btn-default mb-3">Mỗi máy bán hàng có tối đa 3 banner quảng cáo</a>
                            </div>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th style="width: 250px">Hình ảnh quảng cáo</th>
                                <th class="text-center" style="250px">Tên máy/Model</th>
                                <th class="text-center" style="width: 250px">Vị trí đặt máy bán hàng</th>
                                <th class="text-center">Ngày bắt đầu</th>
                                <th class="text-center">Ngày kết thúc</th>
                                @if(auth(MERCHANT)->user()->can('ads.edit'))
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
                ajax: "{{ route('merchant.ads.list') }}",
                order: [[4, 'DESC']],
                columns: [
                    {data: 'image', name: 'image', orderable: false, searchable: false},
                    {data: 'machine_model', name: 'machine_model', orderable: false, },
                    {data: 'machine_address', name: 'machine_address', orderable: false, },
                    {data: 'start_date', name: 'start_date'},
                    {data: 'end_date', name: 'end_date'},
                        @if(auth(MERCHANT)->user()->can('ads.edit'))
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false
                    },
                    @endif
                ]
            });
        });

        function deleteAds(id) {
            Swal.fire({
                title: 'Bạn muốn xóa Quảng cáo này?',
                text: "Hành động này không thể khôi phục!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.value) {
                    axios.put('/ads/' + id + '/delete').then(response => {
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
