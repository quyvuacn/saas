@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị thuê bao của bạn') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <!-- Circle Buttons -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Thông tin các thuê bao của bạn</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable-vti">
                            <thead>
                            <tr>
                                <th class="text-center">Tên máy/Model</th>
                                <th class="text-center">Vị trí đặt máy</th>
                                <th class="text-center">Thông số sản phẩm</th>
                                <th class="text-center">Ngày bắt đầu</th>
                                <th class="text-center">Thời hạn thuê bao</th>
                                <th class="text-center">Tình trạng</th>
                                @if(auth(MERCHANT)->user()->can('subscription.edit'))
                                    <th class="text-center">Gia hạn</th>
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
                ajax: "{{ route('merchant.subscription.list') }}",
                order: [[4, 'ASC']],
                columns: [
                    {data: 'machine_model', name: 'machine_model'},
                    {data: 'machine_address', name: 'machine_address'},
                    {data: 'spec', name: 'spec', orderable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'date_expiration', name: 'date_expiration'},
                    {data: 'expire_status', name: 'expire_status'},
                        @if(auth(MERCHANT)->user()->can('subscription.edit'))
                    {
                        data: 'action', name: 'action', orderable: false, searchable: false
                    },
                    @endif
                ]
            });
            $('body').delegate('.extend-6, .extend-12', 'click', function () {
                Swal.fire({
                    title: 'Bạn muốn gia hạn Thuê bao này?',
                    text: "",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Gia hạn',
                    cancelButtonText: 'Hủy',
                }).then((result) => {
                    if (result.value) {
                        var data = {
                            month: $(this).data('month'),
                            subscription_id: $(this).data('id')
                        };
                        axios.post('/subscription/extend', data).then(response => {
                            if (response.status == 200 && response.data.status) {
                                Swal.fire(
                                    response.data.message,
                                    '',
                                    'success'
                                );
                            } else {
                                Swal.fire(
                                    'Gia hạn không thành công!',
                                    response.data.message,
                                    'error'
                                );
                            }
                            $('#dataTable-vti').DataTable().ajax.reload();
                        });
                    }
                })
            });
        });
    </script>
@endsection
