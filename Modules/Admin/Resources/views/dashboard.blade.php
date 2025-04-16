@extends('admin::layouts.master')

@section('main-content')
    <!-- Page Heading -->

    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        </div>

        <!-- Page Heading -->
        @include('admin::layouts.partials.header-message')

        <!-- Content Row -->
        <div class="row">

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Đăng ký Merchant</div>
                                <a class="btn btn-outline-primary blob-primary" href="{{route('admin.merchant.request')}}">
                                    Có <strong>{{$totalMerchantRequest}}</strong> merchant cần bạn duyệt
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Yêu cầu máy bán hàng</div>
                                <a class="btn btn-outline-success blob-success" href="{{route('admin.machine.request')}}">
                                    Có <strong>{{$totalMerchantRequestMachine}}</strong> yêu cầu máy bán hàng chờ bạn xử lý
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Yêu cầu trả máy bán hàng</div>
                                <a class="btn btn-outline-info blob-success" href="{{route('admin.machine.requestBack')}}">
                                    Có <strong>{{$totalMachineRequestBack}}</strong> yêu cầu trả máy bán hàng chờ bạn xử lý
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Requests Card Example -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Yêu cầu gia hạn thuê bao</div>
                                <a class="btn btn-outline-warning blob-warning" href="{{route('admin.subscription.extend')}}">
                                    Có <strong>{{$totalSubscriptionRequest}}</strong> yêu cầu gia hạn thuê bao chờ duyệt
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Row -->

        <div class="row">

            <!-- Content Column -->
            <div class="col-lg-6 mb-4">

                <!-- Project Card Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tình trạng thuê bao</h6>
                    </div>
                    <div class="card-body">
                        <div class="row"  style="max-height: 300px;overflow-y: scroll">
                            @foreach($listSubscriptionAboutDateToExpire as $v)
                            @php
                            $dayExpire = ceil((strtotime($v->date_expiration) - time()) / (24*60*60));
                            @endphp
                            <div class="col-lg-6 mb-4">
                                <div class="card text-white shadow @if ($dayExpire <= 7) bg-warning-expire @else bg-secondary @endif">
                                    <div class="card-body">
                                        Thuê bao máy bán hàng {{$v->machine->name}}
                                        <div class="text-white-50 small">Đơn vị thuê: <strong>{{$v->merchant->name}}</strong></div>
                                        <div class="text-white-50 small">Hết hạn ngày {{$v->date_expiration->format('d/m/Y')}} <em>(Còn {{$dayExpire}} ngày)</em></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @foreach($listSubscriptionExpire as $v)
                                <div class="col-lg-6 mb-4">
                                    <div class="card text-white shadow bg-warning-expire">
                                        <div class="card-body">
                                            Thuê bao máy bán hàng {{$v->machine->name}}
                                            <div class="text-white-50 small">Đơn vị thuê: <strong>{{$v->merchant->name}}</strong></div>
                                            <div class="text-white-50 small">Hết hạn ngày {{$v->date_expiration->format('d/m/Y')}} <em>(Đã hết hạn)</em></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-lg-6 mb-4">
                <!-- Approach -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Các yêu cầu đang xử lý</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush list-machine-avaiable">
                            @foreach($listProcessing as $v)
                            <li class="list-group-item"><a href="{{route('admin.machine.requestProcessing')}}">{{$v->request_content}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <!-- Content Row -->

        <div class="row">

            <!-- Area Chart -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Máy bán hàng đang hoạt động</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="myAreaChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie Chart -->
            @if(!empty($listLogStatusMachine->count()))
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Tình trạng hoạt động của các máy bán hàng</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <ul class="list-group list-group-flush list-machine-avaiable">
                            @foreach($listLogStatusMachine as $v)
                            <li class="list-group-item">
                                <div class="icon-circle bg-primary float-left mr-2">
                                    <i class="fas fa-file-alt text-white"></i>
                                </div>
                                @php
                                $minute = ceil((time() - strtotime($v->created_at)) / (60));
                                @endphp

                                @if($v->status == $v::ERROR)
                                    Máy bán hàng <strong>{{$v->machine->name ?? ''}}</strong> đã mất kết nối <span class="text-danger">{{$minute}}</span> phút trước
                                @else
                                    Máy bán hàng <strong>{{$v->machine->name ?? ''}}</strong> đã hoạt động <span class="text-success">{{$minute}}</span> phút trước
                                @endif
                            </li>
                            @endforeach
                        </ul>

                    </div>
                </div>
            </div>
            @endif
        </div>

    </div>
@endsection

@section('extra-js')
    {{--Chart--}}
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            axios.post('{{route('admin.getDataChartMachine')}}').then(response => {
                var label = response.data.label;
                var data = response.data.data;
                label.reverse();
                data.reverse();
                showChartPie(label, data);
            }).catch(function (error) {
                showMessageError();
            });
        })

        function showChartPie(label, data) {
            var ctx = document.getElementById("myAreaChart");
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: label,
                    datasets: [{
                        label: "Earnings",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: data,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 25,
                            top: 25,
                            bottom: 0
                        }
                    },
                    scales: {
                        xAxes: [{
                            time: {
                                unit: 'date'
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                maxTicksLimit: 7
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                // max: 50000000,
                                min: 0,
                                maxTicksLimit: 5,
                                padding: 10,
                                // Include a dollar sign in the ticks
                                callback: function(value, index, values) {
                                    return value;
                                }
                            },
                            gridLines: {
                                color: "rgb(234, 236, 244)",
                                zeroLineColor: "rgb(234, 236, 244)",
                                drawBorder: false,
                                borderDash: [2],
                                zeroLineBorderDash: [2]
                            }
                        }],
                    },
                    legend: {
                        display: false
                    },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                return datasetLabel + ': ' + tooltipItem.yLabel;
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection

<style>
    .list-machine-avaiable{
        max-height: 350px;
        overflow-y: scroll;
    }
    .blob-warning {
        margin: 10px;

        box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.2);
        transform: scale(1);
        animation: pulse-warning 2s infinite;
    }

    .bg-warning-expire{
        background-color: lightcoral !important;
    }

    @keyframes pulse-warning {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(255, 177, 66, 0.5);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(255, 177, 66, 0);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(255, 177, 66, 0);
        }
    }

    .blob-success {
        margin: 10px;

        box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.2);
        transform: scale(1);
        animation: pulse-success 2s infinite;
    }

    @keyframes pulse-success {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(0, 102, 0, 0.5);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(0, 102, 0, 0);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(0, 102, 0, 0);
        }
    }

    .blob-primary {
        margin: 10px;

        box-shadow: 0 0 0 0 rgba(255, 0, 0, 0.2);
        transform: scale(1);
        animation: pulse-primary 2s infinite;
    }

    @keyframes pulse-primary {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(0, 76, 153, 0.5);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(0, 76, 153, 0);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(0, 76, 153, 0);
        }
    }
</style>
