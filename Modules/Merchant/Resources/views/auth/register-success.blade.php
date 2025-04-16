@extends('layouts.auth_register')

@section('main-content')
    <div class="container-fluid bg-white">
        <div class="row box-header">
            <div class="container">
                <nav class="navbar navbar-expand-md py-1">
                    <a class="navbar-brand nav-home" title="Trang chủ 1giay.vn" href="https://1giay.vn">
                        <img src="/images/logo.svg" alt="logo 1giay.vn - Một giây là có!" height="60px"/>
                    </a>
                    <button class="navbar-toggler navbar-dark" type="button" data-toggle="collapse"
                            data-target="#main-navigation" onclick="changeBgHeader()">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="main-navigation">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" title="Trang chủ cho thuê máy bán hàng" href="https://1giay.vn/index.html">Trang chủ</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" title="Về chúng tôi" href="https://1giay.vn/ve-chung-toi.html">Về chúng tôi</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" title="Thuê máy bán hàng" href="https://1giay.vn/thue-may-ban-hang.html">
                                    Thuê máy bán hàng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" title="Ứng dụng người dùng" href="https://1giay.vn/ung-dung-nguoi-dung.html">
                                    Ứng dụng người dùng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/" title="Đăng ký thuê máy bán hàng" class="btn btn-primary btn-custom text-uppercase px-4 ">
                                    Đăng ký thuê máy bán hàng
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    <header class="page-header-no-img container-fluid">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <div class="description pt-5">
                        <h2 class="text-white mt-100 text-machine">Thuê máy bán hàng</h2>
                        <p class="py-3 mb-5">
                            Bạn dễ dàng bắt đầu kinh doanh của mình bằng cách đăng ký thuê máy bán hàng của chúng tôi.
                        </p>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <img class="float-right img-vending-machine" alt="Dịch vụ cho thuê máy bán hàng"
                         src="/images/maybanhang.png" width="350px"/>
                </div>
            </div>
        </div>
    </header>

    <section class="bg-white">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 form-machine" style="color: #000; padding: 200px 0;">
                    <h1 class="mb-5 text-center">Bạn đã đăng ký làm người bán hàng thành công!</h1>
                    <h5 class="mb-3 text-center">Cảm ơn bạn đã tin tưởng sử dụng dịch vụ của chúng tôi. Chúng tôi sẽ liên hệ với bạn trong vòng 8 giờ làm việc, để hoàn tất việc đăng ký làm người bán hàng.</h5>
                </div>
            </div>
        </div>
    </section>

    <section class="footer">
        <div class="container">
            <div class="row pt-5 pb-5">
                <div class="col-12 col-xl-4 justify-content-center">
                    <h2 class="text-white my-3">
                        <img src="/images/logo-footer.png" alt="logo 1giay.vn - Một giây là có">
                        <span style="font-size: 20px;line-height: 23px;text-transform: uppercase;">Một giây là có!</span>
                    </h2>
                    <p class="text-white decs-footer">
                        1giay.vn là sản phẩm công nghệ tự động trên nền tảng điện toán đám mây, giúp bạn tối ưu hóa chi phí
                        nhân sự cho cửa hàng 24 giờ mỗi ngày, 7 ngày mỗi tuần.
                    </p>
                    <div class="d-flex">
                        <a href="javascript:void(0);" data-toggle="modal" data-target="#registerModal" class="mr-lg-4 ml-0">
                            <img class="btn-app-small" src="/images/footer_app_store.svg" alt="Tải ứng dụng trên app store">
                        </a>
                        <a href="javascript:void(0);" data-toggle="modal" data-target="#registerModal" class="m-0">
                            <img class="btn-app-small" src="/images/footer_google_play.svg"
                                 alt="tải ứng dụng trên google play">
                        </a>
                    </div>
                </div>
                <div class="col-12 col-xl-3 justify-content-center text-left pt-4 pl-5">
                    <ul class="list-unstyled">
                        <li class="mb-4">
                            <a href="https://1giay.vn/" title="Trang chủ" class="text-white font-weight-bold">Trang chủ</a>
                        </li>
                        <li class="mb-4">
                            <a href="https://1giay.vn/ve-chung-toi.html" title="Về chúng tôi" class="text-white">Về chúng tôi</a>
                        </li>
                        <li class="mb-4">
                            <a href="https://1giay.vn/thue-may-ban-hang.html" title="Thuê máy bán hàng" class="text-white">Thuê máy bán
                                hàng</a>
                        </li>
                        <li>
                            <a href="https://1giay.vn/ung-dung-nguoi-dung.html" title="Ứng dụng người dùng" class="text-white">Ứng dụng người dùng</a>
                        </li>
                    </ul>
                </div>
                <div class="col-12 col-xl-5 justify-content-center">
                    <h5 class="text-white fs-34">Bắt đầu nhận thông báo</h5>
                    <p class="text-white fs-22">Đăng ký nhận thông báo về các chương trình khuyến mại, các chức năng mới của
                        hệ thống bán hàng tự động 1giay.vn</p>
                    <div>
                        <form class="form-inline">
                            <div class="form-group mb-2 mr-3">
                                <input type="email" class="form-control pl-2 pr-4 pt-3 pb-3" id="email"
                                       placeholder="Nhập email của bạn">
                            </div>
                            <button type="submit" class="btn btn-danger mb-2 btn-custom-red">Nhận thông báo</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="container-fluid background-black lg-footer">
        <div class="container">
            <div class="row">
                <div class="col-10 col-lg-6 col-sm-10 text-left text-white fs-16 line-height-80 mb-0">
                    Copyright © 2020 <a class="text-white font-weight-bold" title="1giay.vn"
                                        href="http://1giay.vn">1giay.vn</a>. All rights
                    reserved
                </div>
                <div class="col-2 col-lg-6 col-sm-2 text-right text-white pt-3">
                    <p class="float-right mt-3 mb-0 ml-3">Made in VTI Vietnam</p>
                    <img class="float-right" src="/images/logo.png" alt="logo vti">
                </div>
            </div>
        </div>
    </div>
@endsection
@section('extra-js')
    <script>
        function changeBgHeader() {
            $('.box-header').toggleClass('header-blue');
            $('.navbar-toggler').toggleClass('nav-toggle-custom');
        }

    </script>
@endsection
