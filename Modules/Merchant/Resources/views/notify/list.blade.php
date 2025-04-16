@extends('merchant::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Quản trị thông báo tới khách hàng của merchant') }}</h1>

    @include('merchant::layouts.partials.header-message')

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    @if(auth(MERCHANT)->user()->can('notify.edit'))
                        <div class="row">
                            <div class="col-6">
                                <a href="{{route('merchant.notify.create')}}">
                                    <button class="btn btn-primary">Tạo thông báo mới</button>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    <table class="table table-bordered" id="dataTable-vti" width="100%" cellspacing="0">
                        <thead>
                        <tr>
                            <th style="vertical-align: middle; text-align: center;">
                                ID
                            </th>
                            <th>
                                Tiêu đề
                            </th>
                            <th style="vertical-align: middle; text-align: center;">
                                Thời gian tạo
                            </th>
                            <th style="vertical-align: middle; text-align: center;">
                                Thời gian thông báo
                            </th>
                            <th style="vertical-align: middle; text-align: center;">
                                Thời gian hết hạn
                            </th>
                            @if(auth(MERCHANT)->user()->can('notify.edit'))
                                <th style="vertical-align: middle; text-align: center;">
                                    Sửa
                                </th>
                                <th style="vertical-align: middle; text-align: center;">
                                    Xóa
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

@endsection

@section('extra-js')
    <script>
        $(function () {
            var table = $('#dataTable-vti').DataTable({
                processing: true,
                // serverSide: true,
                ajax: "{{ route('merchant.notify.list') }}",
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'title', name: 'title'},
                    {data: 'created_at', name: 'created_at'}, // Wrong
                    {data: 'time_begin_show', name: 'time_begin_show'},
                    {data: 'time_end_show', name: 'time_end_show'},
                    @if(auth(MERCHANT)->user()->can('notify.edit'))
                    {
                        data: 'edit', name: 'edit', orderable: false, searchable: false
                    },
                    {
                        data: 'delete', name: 'delete', orderable: false, searchable: false
                    },
                    @endif
                ],
                order: [[3, 'DESC']],
            });
        })
        @if(auth(MERCHANT)->user()->can('notify.edit'))
            function deleteNotify(id) {
            Swal.fire({
                title: 'Bạn muốn xóa Thông báo này?',
                text: "Hành động này không thể khôi phục!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.value) {
                    axios.put('/notify/delete/' + id).then(response => {
                        if (response.status == 200 && response.data.status) {
                            showMessageSuccess(response.data.message);
                            $('#dataTable-vti').DataTable().ajax.reload();
                        } else {
                            Swal.fire(
                                'Ops!',
                                response.data.message,
                                'error'
                            );
                            // showMessageError();
                        }
                    });
                }
            })
        }
        @endif
    </script>
@endsection
