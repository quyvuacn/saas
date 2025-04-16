@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Các phiên bản máy bán hàng') }}</h1>

    @include('admin::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Danh sách phiên bản máy bán hàng</h6>
                </div>
                <div class="card-body">
                    <div class="row d-flex mb-2 justify-content-between justify-content-start">
                        <div class="col-3 mt-1">
                            <a href="{{route('admin.app.create')}}" class="btn btn-primary">Thêm phiên bản mới</a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti">
                            <thead>
                            <tr>
                                <th>STT</th>
                                <th>Phiên bản</th>
                                <th>Version Code</th>
                                <th>Ứng dụng</th>
                                <th>Mô tả</th>
                                <th>Ngày tạo</th>
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
                ajax: "{{ route('admin.app.list') }}",
                columns: [
                    {data: 'stt', name: 'admin.id'},
                    {data: 'version', name: 'version'},
                    {data: 'version_code', name: 'version_code'},
                    {data: 'url', name: 'link', orderable: false},
                    {data: 'brief', name: 'brief'},
                    {data: 'created_at', name: 'created_at'},
                ],
                order: [[3, 'DESC']],
            });
        });
    </script>
@endsection
