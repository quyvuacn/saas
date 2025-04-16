<?php

use Modules\Merchant\Classes\Facades\MerchantCan;

?>
@extends('merchant::layouts.master')

@section('main-content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Dashboard') }}</h1>
    </div>

    @include('merchant::layouts.partials.header-message')

    @if (!MerchantCan::do('isApproved'))
        <div class="row">
            <div class="col-lg-12 mb-4">
                <!-- Approach -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thông báo</h6>
                    </div>
                    <div class="card-body">
                        <h3 class="text-left">Merchant đang chờ phê duyệt!</h3>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">KHÁCH HÀNG ĐĂNG KÝ MỚI CHỜ PHÊ DUYỆT</div>
                                <a href="{{route('merchant.user.list')}}" class="btn btn-outline-primary blob-primary">
                                    Có <strong>{{number_format($totalNewCustomers, 0)}}</strong> khách hàng đăng ký mới
                                </a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">DOANH SỐ BÁN HÀNG HÔM NAY</div>
                                <a href="{{route('merchant.machine.history')}}" class="btn btn-outline-success blob-success">
                                    Doanh số bán hôm nay là <strong>{{number_format($totalTodayRevenue, 0)}}
                                        <sup>Vnđ</sup></strong>
                                </a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">NỢ CẦN THU HỒI</div>
                                <a href="{{route('merchant.user.debt')}}" class="btn btn-outline-info blob-success">
                                    Có <strong>{{$totalDebtUsers ?? 0}}</strong> tín chấp cần thu hồi
                                </a></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">SẢN PHẨM TRONG DANH SÁCH BÁN HÀNG</div>
                                <a href="{{route('merchant.product.list')}}" class="btn btn-outline-warning blob-warning">
                                    Có <strong>{{number_format($totalProducts, 0)}}</strong> Sản phẩm trong danh sách
                                </a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Content Column -->
            <div class="col-lg-6 mb-4">
                <!-- Project Card Example -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Tình trạng hàng trên máy</h6>
                    </div>
                    <div class="card-body">
                        <div class="product-status-machine">
                            @if($productOnMachines->count())
                                @foreach($productOnMachines as $key => $product)
                                    <h4 class="small font-weight-bold">{{$product->name??'---'}}
                                        <span class="float-right">{{$product->percent}}%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar {{machineProductWarning($product->percent)}}" role="progressbar" style="width: {{$product->percent}}%" aria-valuenow="{{$product->percent}}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                @endforeach
                            @else
                                <h4 class="small font-weight-bold">Dữ liệu đang cập nhật...!</h4>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <!-- Approach -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Thông báo</h6>
                    </div>
                    <div class="card-body">
                        <div class="latest-selling-transaction">
                            @if($latestSellingTransactions->count())
                                <ul class="list-group list-group-flush">
                                    @foreach($latestSellingTransactions as $transaction)
                                        <li class="list-group-item">Máy bán hàng
                                            <strong>{{$transaction->machine->name??'---'}}</strong> đã bán một
                                            @php
                                                $products = json_decode($transaction->products);
                                            @endphp
                                            <strong>{{$products && isset($products[0]) && isset($products[0]->name) ? $products[0]->name : '---'}}</strong> cho {{$transaction->buyUser->email ?? '---' }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <h4 class="small font-weight-bold">Dữ liệu đang cập nhật...!</h4>
                            @endif
                        </div>
                        <a href="{{route('merchant.machine.history')}}" class="btn btn-outline-primary mt-3 btn-sm btn-block">Xem tất cả thông báo</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Area Chart -->
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow mb-4">
                    <!-- Card Header - Dropdown -->
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Doanh số bán hàng tuần qua</h6>
                    </div>
                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="chart-area">
                            <div class="chartjs-size-monitor">
                                <div class="chartjs-size-monitor-expand">
                                    <div class=""></div>
                                </div>
                                <div class="chartjs-size-monitor-shrink">
                                    <div class=""></div>
                                </div>
                            </div>
                            <canvas id="myAreaChart" width="1037" height="320" class="chartjs-render-monitor" style="display: block; width: 1037px; height: 320px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Pie Chart -->
            <?php /* ?>
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Tình trạng hoạt động của các máy bán hàng</h6>
                    </div>
                    <div class="card-body">
                        <div class="status-machine">
                            <ul class="list-group list-group-flush list-machine-avaiable">
                                @if($listLogStatusMachine->count())
                                    @foreach($listLogStatusMachine as $status)
                                        <li class="list-group-item">
                                            <div class="icon-circle bg-primary float-left mr-2">
                                                <i class="fas fa-file-alt text-white"></i>
                                            </div>
                                            @php
                                                $minute = ceil((time() - strtotime($status->created_at)) / (60));
                                            @endphp
                                            @if($status->status == $status::ERROR)
                                                Máy bán hàng
                                                <strong>{{$status->machine->name ?? ''}}</strong> đã mất kết nối
                                                <span class="text-danger">{{$minute}}</span> phút trước
                                            @else
                                                Máy bán hàng
                                                <strong>{{$status->machine->name ?? ''}}</strong> đã hoạt động
                                                <span class="text-success">{{$minute}}</span> phút trước
                                            @endif
                                        </li>
                                    @endforeach
                                @else
                                    <li class="list-group-item">
                                        Đang cập nhật...!
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php */ ?>
        </div>
    @endif

@endsection

@section('extra-js')
    <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
    <script>
        $(function () {
            axios.post('/chart-dashboard-revenue').then(response => {
                var label = response.data.label;
                var data = response.data.data;
                showChartPie(label, data);
            }).catch(function (error) {
                showMessageError();
            });
        })

        function number_format(number, decimals, dec_point, thousands_sep) {
            // *     example: number_format(1234.56, 2, ',', ' ');
            // *     return: '1 234,56'
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                s = '',
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            // Fix for IE parseFloat(0.55).toFixed(0) = 0;
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        function showChartPie(label, data) {
            if(!$('#myAreaChart').length){
                return;
            }
            var ctx = document.getElementById("myAreaChart");
            var myLineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: label,
                    datasets: [{
                        label: "Doanh số",
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
                                stepSize: 100000,
                                maxTicksLimit: 15,
                                padding: 10,
                                callback: function (value, index, values) {
                                    return number_format(value) + ' đ';
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
                            label: function (tooltipItem, chart) {
                                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                return datasetLabel + number_format(tooltipItem.yLabel) + ' đ';
                            }
                        }
                    }
                }
            });
        }
    </script>
@endsection
