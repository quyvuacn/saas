<?php

return [
    'name' => 'Admin',
    'payment_method' => [
        'Chuyển khoản ngân hàng',
        'Thanh toán trực tiếp'
    ],
    'date_about_to_expire' => 15,
    'route_action' => [
        'admin.account.store' => [
            'action' => 'Thêm mới',
            'function' => 'Tạo tài khoản quản trị mới'
        ],
        'admin.account.create' => [
            'action' => 'Thêm mới',
            'function' => 'Tạo tài khoản quản trị mới'
        ],
        'admin.account.update' => [
            'action' => 'Sửa',
            'function' => 'Sửa tài khoản quản trị'
        ],
        'admin.account.edit' => [
            'action' => 'Sửa',
            'function' => 'Sửa tài khoản quản trị'
        ],
        'admin.account.updateProfile' => [
            'action' => 'Sửa',
            'function' => 'Sửa thông tin tài khoản'
        ],
        'admin.account.permissionAjax' => [
            'action' => 'Sửa',
            'function' => 'Sửa phân quyền tài khoản'
        ],
        'admin.account.permissionChange' => [
            'action' => 'Sửa',
            'function' => 'Sửa phân quyền tài khoản'
        ],
        'admin.account.delete' => [
            'action' => 'Xóa',
            'function' => 'Xóa tài khoản quản trị'
        ],
        'admin.account.toggleStatus' => [
            'action' => 'Sửa',
            'function' => 'Thay đổi trạng thái tài khoản quản trị'
        ],
        'admin.account.toggle' => [
            'action' => 'Sửa',
            'function' => 'Thay đổi trạng thái tài khoản quản trị'
        ],
        'admin.machine.createPost' => [
            'action' => 'Thêm mới',
            'function' => 'Thêm mới máy bán hàng'
        ],
        'admin.machine.update' => [
            'action' => 'Sửa',
            'function' => 'Sửa thông tin máy bán hàng'
        ],
        'admin.machine.delete' => [
            'action' => 'Xóa',
            'function' => 'Xóa máy bán hàng'
        ],
        'admin.machine.approveRequest' => [
            'action' => 'Duyệt yêu cầu',
            'function' => 'Duyệt yêu cầu cấp máy bán hàng'
        ],
        'admin.machine.finalApproveRequest' => [
            'action' => 'Duyệt yêu cầu',
            'function' => 'Duyệt yêu cầu cấp máy bán hàng'
        ],
        'admin.machine.finalRequestProcessing' => [
            'action' => 'Duyệt yêu cầu',
            'function' => 'Hoàn tất duyệt yêu cầu cấp máy bán hàng'
        ],
        'admin.machine.approveRequestBack' => [
            'action' => 'Duyệt yêu cầu',
            'function' => 'Duyệt yêu cầu trả máy bán hàng'
        ],
        'admin.machine.finalApproveRequestBack' => [
            'action' => 'Duyệt yêu cầu',
            'function' => 'Duyệt yêu cầu trả máy bán hàng'
        ],
        'admin.machine.finalRequestBackProcessing' => [
            'action' => 'Duyệt yêu cầu',
            'function' => 'Hoàn tất duyệt yêu cầu trả máy bán hàng'
        ],
        'admin.machine.cancelRequestBack' => [
            'action' => 'Hủy yêu cầu',
            'function' => 'Hủy yêu cầu trả máy bán hàng'
        ],
        'admin.machine.createAttributesPost' => [
            'action' => 'Thêm mới',
            'function' => 'Thêm cấu hình thông tin máy'
        ],
        'admin.merchant.store' => [
            'action' => 'Sửa',
            'function' => 'Sửa thông tin Merchant'
        ],
        'admin.merchant.delete' => [
            'action' => 'Xóa',
            'function' => 'Xóa tài khoản Merchant'
        ],
        'admin.merchant.approveRequest' => [
            'action' => 'Duyệt yêu cầu',
            'function' => 'Duyệt yêu cầu đăng ký Merchant'
        ],
        'admin.merchant.finalApproveRequest' => [
            'action' => 'Duyệt yêu cầu',
            'function' => 'Hoàn tất duyệt yêu cầu đăng ký Merchant'
        ],
        'admin.subscription.store' => [
            'action' => 'Thêm mới',
            'function' => 'Thêm mới yêu cầu gia hạn thuê bao'
        ],
        'admin.subscription.update' => [
            'action' => 'Sửa',
            'function' => 'Sửa ngày hết hạn thuê bao'
        ],
        'admin.subscription.approveRequest' => [
            'action' => 'Duyệt yêu cầu',
            'function' => 'Duyệt yêu cầu gia hạn thuê bao'
        ],
        'admin.subscription.finalApproveRequest' => [
            'action' => 'Duyệt yêu cầu',
            'function' => 'Hoàn tất duyệt yêu cầu gia hạn thuê bao'
        ],
        'admin.app.store' => [
            'action' => 'Thêm mới',
            'function' => 'Thêm mới phiên bản máy'
        ],
    ]
];
