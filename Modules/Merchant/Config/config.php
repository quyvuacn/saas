<?php

return [
    'credit_quota'        => [
        'text'  => [
            'lv_1' => 'Một triệu đồng',
            'lv_2' => 'Hai triệu đồng',
        ],
        'price' => [
            'lv_1' => 1000000,
            'lv_2' => 2000000,
        ],
    ],
    'subscription_extend' => [
        'month' => [
            '3'  => 3,
            '6'  => 6,
            '9'  => 9,
            '12' => 12,
            '24' => 24,
        ],
        'price' => [
            '3'  => 3000000,
            '6'  => 18000000,
            '9'  => 26000000,
            '12' => 32000000,
            '24' => 68000000,
        ],
        'extra' => [
            '3'  => 0,
            '6'  => 0,
            '9'  => 0,
            '12' => 1,
            '24' => 2,
        ],
    ],
    'min_credit_quote'    => 50000,
    'max_credit_quote'    => 10000000,
    'coin_recharge'       => [
        '50000'    => 50000,
        '100000'   => 100000,
        '200000'   => 200000,
        '500000'   => 500000,
        '1000000'  => 1000000,
        '2000000'  => 2000000,
        '5000000'  => 5000000,
        '10000000' => 10000000,
    ],
    'max_bank'            => 6,
    'max_extra_account'   => 10,

    'excel_replace' => [
        'source' => [
            'There was an error on row',
        ],
        'des'    => [
            'Có lỗi xảy ra tại [row]',
        ],
    ],

    'route_action' => [
        'merchant.user.create'                   => [
            'action'   => 'Thêm mới',
            'function' => 'Tạo tài khoản khách hàng',
        ],
        'merchant.user.import'                   => [
            'action'   => 'Thêm mới',
            'function' => 'Tạo tài khoản khách hàng bằng excel',
        ],
        'merchant.user.delete'                   => [
            'action'   => 'Xóa',
            'function' => 'Xóa tài khoản khách hàng',
        ],
        'merchant.user.deleteCredit'             => [
            'action'   => 'Xóa tín dụng',
            'function' => 'Xóa hạn mức tín dụng khách hàng',
        ],
        'merchant.user.quickApprove'             => [
            'action'   => 'Duyệt nhanh',
            'function' => 'Chức năng Duyệt yêu cầu nạp coin',
        ],
        'merchant.user.deleteCoinRequest'        => [
            'action'   => 'Xóa',
            'function' => 'Xóa yêu cầu nạp coin',
        ],
        'merchant.user.rechargeStore'            => [
            'action'   => 'Thêm mới',
            'function' => 'Tạo yêu cầu nạp coin',
        ],
        'merchant.user.approve'                  => [
            'action'   => 'Duyệt yêu cầu',
            'function' => 'Duyệt hạn mức tín dụng',
        ],
        'merchant.user.approveOption'            => [
            'action'   => 'Duyệt tùy chỉnh',
            'function' => 'Duyệt yêu cầu nạp coin',
        ],
        'merchant.account.permissionChange'      => [
            'action'   => 'Sửa',
            'function' => 'Sửa phân quyền tài khoản phụ',
        ],
        'merchant.account.delete'                => [
            'action'   => 'Xoá',
            'function' => 'Xóa tài khoản phụ',
        ],
        'merchant.account.edit'                  => [
            'action'   => 'Sửa',
            'function' => 'Sửa thông tin tài khoản phụ',
        ],
        'merchant.account.create'                => [
            'action'   => 'Thêm mới',
            'function' => 'Thêm mới tài khoản phụ',
        ],
        'merchant.account.profile'               => [
            'action'   => 'Sửa',
            'function' => 'Sửa thông tin cá nhân',
        ],
        'merchant.account.setting'               => [
            'action'   => 'Sửa',
            'function' => 'Sửa thông tin Merchant',
        ],
        'merchant.machine.updateRequest'         => [
            'action'   => 'Sửa',
            'function' => 'Sửa thông tin yêu cầu cung cấp máy bán hàng',
        ],
        'merchant.machine.request'               => [
            'action'   => 'Thêm mới',
            'function' => 'Tạo yêu cầu cung cấp máy bán hàng',
        ],
        'merchant.machine.requestBack'           => [
            'action'   => 'Thêm mới',
            'function' => 'Tạo yêu cầu trả máy bán hàng',
        ],
        'merchant.machine.deleteRequest'         => [
            'action'   => 'Xóa',
            'function' => 'Xóa yêu cầu cung cấp máy bán hàng',
        ],
        'merchant.machine.changeAddress'         => [
            'action'   => 'Sửa',
            'function' => 'Sửa vị trí đặt máy',
        ],
        'merchant.staff.delete'                  => [
            'action'   => 'Xóa',
            'function' => 'Xóa cài đặt tín dụng',
        ],
        'merchant.staff.bulkDelete'              => [
            'action'   => 'Xóa',
            'function' => 'Xóa nhiều cài đặt tín dụng',
        ],
        'merchant.staff.edit'                    => [
            'action'   => 'Sửa',
            'function' => 'Sửa cài đặt tín dụng',
        ],
        'merchant.staff.edit'                    => [
            'action'   => 'Sửa',
            'function' => 'Sửa cài đặt tín dụng',
        ],
        'merchant.staff.export'                  => [
            'action'   => 'Export',
            'function' => 'Export tín dụng',
        ],
        'merchant.staff.import'                  => [
            'action'   => 'Import',
            'function' => 'Import tín dụng',
        ],
        'merchant.ads.update'                    => [
            'action'   => 'Sửa',
            'function' => 'Sửa cài đặt quảng cáo',
        ],
        'merchant.ads.create'                    => [
            'action'   => 'Thêm mới',
            'function' => 'Thêm mới cài đặt quảng cáo',
        ],
        'merchant.ads.delete'                    => [
            'action'   => 'Xóa',
            'function' => 'Xóa cài đặt quảng cáo',
        ],
        'merchant.product.update'                => [
            'action'   => 'Sửa',
            'function' => 'Sửa thông tin sản phẩm',
        ],
        'merchant.product.create'                => [
            'action'   => 'Thêm mới',
            'function' => 'Thêm mới sản phẩm',
        ],
        'merchant.product.delete'                => [
            'action'   => 'Xóa',
            'function' => 'Xóa sản phẩm',
        ],
        'merchant.product.togglePack'            => [
            'action'   => 'Sửa',
            'function' => 'Toggle Active Pack',
        ],
        'merchant.product.updateMachineProducts' => [
            'action'   => 'Sửa',
            'function' => 'Sửa thông tin sản phẩm trên máy bán hàng',
        ],
        'merchant.subscription.extend'           => [
            'action'   => 'Thêm mới',
            'function' => 'Tạo yêu cầu gia hạn thuê bao',
        ],
        'merchant.notify.store'                  => [
            'action'   => 'Thêm mới',
            'function' => 'Tạo thông báo cho khách hàng merchant',
        ],
        'merchant.notify.update'                 => [
            'action'   => 'Sửa',
            'function' => 'Sửa thông báo cho khách hàng merchant',
        ],
        'merchant.notify.delete'                 => [
            'action'   => 'Xóa',
            'function' => 'Xóa thông báo cho khách hàng merchant',
        ],
        'merchant.user.exportUser'               => [
            'action'   => 'Tải file',
            'function' => 'Tải file Excel List User',
        ],
        'merchant.user.exportDebt'               => [
            'action'   => 'Xuất file',
            'function' => 'Xuất file thu hồi công nợ',
        ],
        'merchant.user.debtCollectionDisable'    => [
            'action'   => 'Disable',
            'function' => 'Disable thu hồi công nợ',
        ],
        'merchant.user.debtCollectionActivation' => [
            'action'   => 'Active',
            'function' => 'Active thu hồi công nợ',
        ],
        'merchant.user.debtReceived'             => [
            'action'   => 'Update',
            'function' => 'Xác nhận thu hồi công nợ',
        ],
    ],
];
