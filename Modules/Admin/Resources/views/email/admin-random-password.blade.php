<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" le>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>[1giay.vn] - Đặt lại mật khẩu</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900italic,900' rel='stylesheet' type='text/css'>
</head>
<body style="margin: 0; padding: 0; font-family: Roboto">
<style>
    .reset-password {
        display: inline-block;
        padding: 15px 20px;
        background: #005c8c;
        margin-top: 20px;
        border-radius: 10px;
        color: #fff;
        text-decoration: none;
        font-size: 20px;
    }
    .reset-password:hover{
        opacity: 0.8;
    }
</style>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="800" class="">
    <tr>
        <td align="center" bgcolor="#005c8c" style="padding: 15px 30px;">
            <table border="0" cellpadding="0" cellspacing="0" width="360">
                <tr>
                    <td align="center" width="30%">
                        <img src="https://1giay.vn/images/logo-footer.png" alt="1 Giây là có" width="60" height="60" style="display: block; background: #fff; border-radius: 5px" />
                    </td>
                    <td align="center" width="70%" style="vertical-align: bottom">
                        <h1 style="color: #fff; margin: 0">Một giây là có!</h1>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td bgcolor="#ffffff" style="padding: 100px 30px 100px 30px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td align="center">
                        <h2>@if(!empty($type))
                                Thay đổi mật khẩu
                            @else
                                Đăng ký tài khoản admin
                            @endif
                        </h2>
                    </td>
                </tr>
                <tr>
                    <td>
                        @if(!empty($type))
                            Tài khoản {{$account->name}} vừa thay đổi mật khẩu thành công.
                        @else
                            Email {{$account->email}} đã đăng ký thành công tài khoản Admin.
                        @endif
                        <br/>
                        Mật khẩu đăng nhập: {{$password}}
                        <br/>
                        Vui lòng liên hệ với admin để biết thêm chi tiết.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td bgcolor="#000" style="padding: 15px 30px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="75%" style="color: #fff">
                        &reg; VTI JOINT STOCK COMPANY<br />
                        Copyright © 2020 1giay.vn. All rights reserved
                    </td>
                    <td align="right">
                        <table border="0" cellpadding="0" cellspacing="0">
                            <tr>
                                <td>
                                    <a href="javascript:void(0);">
                                        <img src="https://1giay.vn/images/btn-app-store.png" alt="Appstore" width="180" height="77" style="display: block;" border="0" />
                                    </a>
                                </td>
                                <td style="font-size: 0; line-height: 0;" width="20">&nbsp;</td>
                                <td>
                                    <a href="javascript:void(0);">
                                        <img src="https://1giay.vn/images/btn-ch-play.png" alt="Android" width="180" height="77" style="display: block;" border="0" />
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

