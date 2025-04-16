<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create roles
        $roles = [
            [
                'id' => 1,
                'name' => 'Super Admin',
                'alias' => 'super_admin',
                'group' => 'admin',
                'status' => 1,
                'is_deleted' => 0,
                'order' => 1
            ],
            [
                'id' => 2,
                'name' => 'Admin',
                'alias' => 'admin',
                'group' => 'admin',
                'status' => 1,
                'is_deleted' => 0,
                'order' => 2
            ],
            [
                'id' => 3,
                'name' => 'Merchant',
                'alias' => 'merchant',
                'group' => 'merchant',
                'status' => 1,
                'is_deleted' => 0,
                'order' => 3
            ]
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert($role);
        }

        // Create permissions with role assignments
        $permissions = [
            // Super Admin permissions (role_id = 1)
            ['permission_name' => 'dashboard', 'description' => 'Xem dashboard', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'dashboard.list', 'description' => 'Xem danh sách dashboard', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'user.list', 'description' => 'Xem danh sách người dùng', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'user.edit', 'description' => 'Sửa người dùng', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'user.change', 'description' => 'Thay đổi trạng thái người dùng', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'user.create', 'description' => 'Tạo người dùng mới', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'user.show', 'description' => 'Xem chi tiết người dùng', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'account.list', 'description' => 'Xem danh sách tài khoản', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'account.edit', 'description' => 'Sửa tài khoản', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'account.change', 'description' => 'Thay đổi trạng thái tài khoản', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'account.create', 'description' => 'Tạo tài khoản mới', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'account.show', 'description' => 'Xem chi tiết tài khoản', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'permission.list', 'description' => 'Xem danh sách phân quyền', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'permission.edit', 'description' => 'Chỉnh sửa phân quyền', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'permission.change', 'description' => 'Thay đổi trạng thái phân quyền', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'account.is_super_admin', 'description' => 'Check Super Admin', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'account.history', 'description' => 'View Account History', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],
            ['permission_name' => 'super.admin', 'description' => 'Super Admin', 'status' => 1, 'is_deleted' => 0, 'role_id' => 1],

            // Admin permissions (role_id = 2)
            ['permission_name' => 'merchant.list', 'description' => 'List Merchants', 'status' => 1, 'is_deleted' => 0, 'role_id' => 2],
            ['permission_name' => 'merchant.edit', 'description' => 'Edit Merchants', 'status' => 1, 'is_deleted' => 0, 'role_id' => 2],
            ['permission_name' => 'merchant.change', 'description' => 'Change Merchant Status', 'status' => 1, 'is_deleted' => 0, 'role_id' => 2],
            ['permission_name' => 'merchant.show', 'description' => 'Show Merchant Details', 'status' => 1, 'is_deleted' => 0, 'role_id' => 2],
            ['permission_name' => 'merchant_request.list', 'description' => 'List Merchant Requests', 'status' => 1, 'is_deleted' => 0, 'role_id' => 2],
            ['permission_name' => 'merchant_request.edit', 'description' => 'Approve Merchant Requests', 'status' => 1, 'is_deleted' => 0, 'role_id' => 2],
            ['permission_name' => 'machine.processing', 'description' => 'Process Machine Operations', 'status' => 1, 'is_deleted' => 0, 'role_id' => 2],
            ['permission_name' => 'machine.app_version_edit', 'description' => 'Edit Machine App Version', 'status' => 1, 'is_deleted' => 0, 'role_id' => 2],
            ['permission_name' => 'machine.app_version_list', 'description' => 'List Machine App Versions', 'status' => 1, 'is_deleted' => 0, 'role_id' => 2],

            // Merchant permissions (role_id = 3)
            ['permission_name' => 'machine.list', 'description' => 'Xem danh sách máy', 'status' => 1, 'is_deleted' => 0, 'role_id' => 3],
            ['permission_name' => 'machine.edit', 'description' => 'Sửa thông tin máy', 'status' => 1, 'is_deleted' => 0, 'role_id' => 3],
            ['permission_name' => 'machine.change', 'description' => 'Thay đổi trạng thái máy', 'status' => 1, 'is_deleted' => 0, 'role_id' => 3],
            ['permission_name' => 'machine.show', 'description' => 'Xem chi tiết máy', 'status' => 1, 'is_deleted' => 0, 'role_id' => 3],
            ['permission_name' => 'machine_request.list', 'description' => 'List Machine Requests', 'status' => 1, 'is_deleted' => 0, 'role_id' => 3],
            ['permission_name' => 'machine_request.edit', 'description' => 'Approve Machine Requests', 'status' => 1, 'is_deleted' => 0, 'role_id' => 3],
            ['permission_name' => 'machine_request_back.list', 'description' => 'List Machine Request Backlogs', 'status' => 1, 'is_deleted' => 0, 'role_id' => 3],
            ['permission_name' => 'machine_request_back.edit', 'description' => 'Approve Machine Request Backlogs', 'status' => 1, 'is_deleted' => 0, 'role_id' => 3],
            ['permission_name' => 'subscription.list', 'description' => 'Xem danh sách gói dịch vụ', 'status' => 1, 'is_deleted' => 0, 'role_id' => 3],
            ['permission_name' => 'subscription.show', 'description' => 'Xem chi tiết gói dịch vụ', 'status' => 1, 'is_deleted' => 0, 'role_id' => 3],
            ['permission_name' => 'subscription_request.list', 'description' => 'List Subscription Requests', 'status' => 1, 'is_deleted' => 0, 'role_id' => 3],
            ['permission_name' => 'subscription_request.edit', 'description' => 'Approve Subscription Requests', 'status' => 1, 'is_deleted' => 0, 'role_id' => 3]
        ];

        foreach ($permissions as $permission) {
            DB::table('permissions')->insert($permission);
        }
    }
} 