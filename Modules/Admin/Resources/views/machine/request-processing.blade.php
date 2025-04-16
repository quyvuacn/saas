@extends('admin::layouts.master')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Danh sách các yêu cầu đang xử lý') }}</h1>

    @include('admin::layouts.partials.header-message')

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Các yêu cầu đang chờ VTI xử lý</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable1"  width="100%" cellspacing="0">
                    <thead>
                    <tr>
                        <th>
                            <p>Nội dung yêu cầu</p>
                            <p>Người yêu cầu</p>
                        </th>
                        <th class="text-center">Tên máy</th>
                        <th class="text-center">Loại request</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-center">Ngày yêu cầu</th>
                        <th>Ngày hoàn thành dự kiến</th>
                        <th class="text-center">Tình trạng</th>
                        <th class="text-center">Chức năng</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($result as $v)
                        <tr>
                            <td>
                                <p>{{$v->request_content}}</p>
                                <p>{{$v->merchant_name}}</p>
                            </td>
                            <td class="text-center">{{$v->machine_name}}</td>
                            <td class="text-center">{{$arrType[$v->type]}}</td>
                            <td class="text-center">{{$v->count}}</td>
                            <td class="text-center" data-sort="{{strtotime($v->created_at)}}">{{date('d/m/Y', strtotime($v->created_at))}}</td>
                            <td class="text-center" data-sort="{{strtotime($v->date_success)}}">{{date('d/m/Y', strtotime($v->date_success))}}</td>
                            <td class="text-center">
                                @if ($v['type'] == 'machine_request_back')
                                    <label class="badge badge-info">
                                        {{$arrStatus[$v->type]}}
                                    </label>
                                @else
                                    <label class="badge badge-warning">
                                        {{$arrStatus[$v->type]}}
                                    </label>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($v['type'] == 'machine_request_back')
                                    <button onclick="finalApproveRequestBackMachine({{$v->id}})" class="btn btn-primary mb-2" style="min-width: 100px">
                                        Hoàn tất
                                    </button>
                                @else
                                    <button onclick="approveRequest({{$v->id}})" class="btn btn-primary mb-2" style="min-width: 100px">
                                        Hoàn tất
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection


@section('extra-js')
    <script>
        $(document).ready(function() {
            $('#dataTable1').DataTable({
                "order": [[ 2, "desc" ]]
            });
        });

        function finalApproveRequestBackMachine(machineId) {
            Swal.fire({
                title: '',
                text: "Bạn có chắc đã hoàn tất thu hồi máy",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xác nhận'
            }).then((result) => {
                if (result.value) {
                    loading();
                    var url = '/machine/final-request-back-processing/' + machineId;
                    axios.post(url).then(response => {
                        hideLoading();
                        if(response.data.status == 1) {
                            showMessageSuccess('Thu hồi máy thành công');
                            setTimeout(function () {
                                location.reload()
                            }, 2000)
                        } else {
                            showMessageError();
                        }
                    }).catch(function (error) {
                        hideLoading();
                        setTimeout(function () {
                            showMessageError();
                        }, 100)
                    });
                }
            })
        }

        function approveRequest(id) {
            Swal.fire({
                title: '',
                text: "Bạn có chắc đã hoàn tất lắp đặt máy",
                icon: 'success',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Xác nhận'
            }).then((result) => {
                if (result.value) {
                    var url = '/machine/final-request-processing/' + id;
                    axios.post(url).then(response => {
                        if(response.data.status == 1) {
                            showMessageSuccess('Thay đổi trạng thái thành công');
                            setTimeout(function () {
                                location.reload()
                            }, 2000)
                        } else {
                            showMessageError();
                        }
                    }).catch(function (error) {
                        showMessageError();
                    });
                }
            })
        }
    </script>
@endsection
