<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing data
        DB::table('account_permission')->truncate();
        DB::table('permissions')->truncate();
        DB::table('admin')->truncate();
        DB::table('merchant')->truncate();
        DB::table('user')->truncate();
        DB::table('machine')->truncate();
        DB::table('product')->truncate();
        DB::table('subscription')->truncate();
        DB::table('roles')->truncate();
        DB::table('role_permissions')->truncate();
        DB::table('merchant_permission')->truncate();
        DB::table('log_action_admin')->truncate();
        DB::table('log_status_machine')->truncate();
        DB::table('machine_request_back')->truncate();
        DB::table('user')->truncate();

        // Insert permissions
        DB::table('permissions')->insert([
            ['id' => 1, 'permission_name' => 'dashboard', 'permission_desc' => 'Xem dashboard', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 2, 'permission_name' => 'dashboard.list', 'permission_desc' => 'Xem danh sách dashboard', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 3, 'permission_name' => 'user.list', 'permission_desc' => 'Xem danh sách người dùng', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 4, 'permission_name' => 'user.edit', 'permission_desc' => 'Sửa người dùng', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 5, 'permission_name' => 'user.change', 'permission_desc' => 'Thay đổi trạng thái người dùng', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 6, 'permission_name' => 'user.create', 'permission_desc' => 'Tạo người dùng mới', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 7, 'permission_name' => 'user.show', 'permission_desc' => 'Xem chi tiết người dùng', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 8, 'permission_name' => 'account.list', 'permission_desc' => 'Xem danh sách tài khoản', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 9, 'permission_name' => 'account.edit', 'permission_desc' => 'Sửa tài khoản', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 10, 'permission_name' => 'account.change', 'permission_desc' => 'Thay đổi trạng thái tài khoản', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 11, 'permission_name' => 'account.create', 'permission_desc' => 'Tạo tài khoản mới', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 12, 'permission_name' => 'account.show', 'permission_desc' => 'Xem chi tiết tài khoản', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 13, 'permission_name' => 'machine.list', 'permission_desc' => 'Xem danh sách máy', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 14, 'permission_name' => 'machine.edit', 'permission_desc' => 'Sửa thông tin máy', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 15, 'permission_name' => 'machine.change', 'permission_desc' => 'Thay đổi trạng thái máy', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 16, 'permission_name' => 'machine.create', 'permission_desc' => 'Tạo máy mới', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 17, 'permission_name' => 'machine.show', 'permission_desc' => 'Xem chi tiết máy', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 18, 'permission_name' => 'subscription.list', 'permission_desc' => 'Xem danh sách gói dịch vụ', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 19, 'permission_name' => 'subscription.edit', 'permission_desc' => 'Sửa gói dịch vụ', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 20, 'permission_name' => 'subscription.change', 'permission_desc' => 'Thay đổi trạng thái gói dịch vụ', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 21, 'permission_name' => 'subscription.create', 'permission_desc' => 'Tạo gói dịch vụ mới', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 22, 'permission_name' => 'subscription.show', 'permission_desc' => 'Xem chi tiết gói dịch vụ', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:07:14', 'updated_at' => '2025-03-31 07:07:14'],
            ['id' => 23, 'permission_name' => 'permission.list', 'permission_desc' => 'Xem danh sách phân quyền', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:09:49', 'updated_at' => '2025-03-31 07:09:49'],
            ['id' => 24, 'permission_name' => 'permission.edit', 'permission_desc' => 'Chỉnh sửa phân quyền', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:09:49', 'updated_at' => '2025-03-31 07:09:49'],
            ['id' => 25, 'permission_name' => 'permission.change', 'permission_desc' => 'Thay đổi trạng thái phân quyền', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:09:49', 'updated_at' => '2025-03-31 07:09:49'],
            ['id' => 26, 'permission_name' => 'account.is_super_admin', 'permission_desc' => 'Check Super Admin', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 27, 'permission_name' => 'account.history', 'permission_desc' => 'View Account History', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 28, 'permission_name' => 'merchant.list', 'permission_desc' => 'List Merchants', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 29, 'permission_name' => 'merchant.edit', 'permission_desc' => 'Edit Merchants', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 30, 'permission_name' => 'merchant.change', 'permission_desc' => 'Change Merchant Status', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 31, 'permission_name' => 'merchant.show', 'permission_desc' => 'Show Merchant Details', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 32, 'permission_name' => 'merchant_request.list', 'permission_desc' => 'List Merchant Requests', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 33, 'permission_name' => 'merchant_request.edit', 'permission_desc' => 'Approve Merchant Requests', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 34, 'permission_name' => 'machine.processing', 'permission_desc' => 'Process Machine Operations', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 35, 'permission_name' => 'machine.app_version_edit', 'permission_desc' => 'Edit Machine App Version', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 36, 'permission_name' => 'machine.app_version_list', 'permission_desc' => 'List Machine App Versions', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 37, 'permission_name' => 'machine_request.list', 'permission_desc' => 'List Machine Requests', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 38, 'permission_name' => 'machine_request.edit', 'permission_desc' => 'Approve Machine Requests', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 39, 'permission_name' => 'machine_request_back.list', 'permission_desc' => 'List Machine Request Backlogs', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 40, 'permission_name' => 'machine_request_back.edit', 'permission_desc' => 'Approve Machine Request Backlogs', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 41, 'permission_name' => 'subscription_request.list', 'permission_desc' => 'List Subscription Requests', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 42, 'permission_name' => 'subscription_request.edit', 'permission_desc' => 'Approve Subscription Requests', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:12:08', 'updated_at' => '2025-03-31 07:12:08'],
            ['id' => 43, 'permission_name' => 'super.admin', 'permission_desc' => 'Super Admin', 'status' => 1, 'is_deleted' => 0, 'created_at' => '2025-03-31 07:19:50', 'updated_at' => '2025-03-31 07:19:50']
        ]);

        // Insert admin
        DB::table('admin')->insert([
            [
                'id' => 1, 'email' => 'admin@example.com', 'account' => 'admin',
                'password' => Hash::make('12345678'),
                'is_required_change_password' => 0, 'created_at' => '2025-03-31 07:03:20',
                'created_by' => 1, 'updated_at' => '2025-03-31 07:28:44', 'updated_by' => 1,
                'is_deleted' => 0, 'status' => 1, 'name' => 'System Administrator',
                'last_login' => '2025-03-31 07:28:44'
            ]
        ]);

        // Insert account permissions
        for ($i = 1; $i <= 43; $i++) {
            DB::table('account_permission')->insert([
                [
                    'account_id' => 1,
                    'permission_id' => $i,
                    'table' => 'admin',
                    'created_at' => '2025-03-31 07:25:13',
                    'updated_at' => '2025-03-31 07:25:13'
                ]
            ]);
        }

        // Insert merchantß
        DB::table('merchant')->insert([
            [
                'id' => 1, 'account' => 'merchant', 'email' => 'merchant@example.com',
                'password' => Hash::make('11111111'),
                'phone' => '0987654321', 'machine_count' => 0, 'parent_id' => 0,
                'status' => 3, 'created_at' => '2025-03-31 07:03:20',
                'updated_at' => '2025-03-31 07:34:42', 'updated_by' => 1,
                'is_deleted' => 0, 'name' => 'Demo Merchant'
            ]
        ]);

        // Insert merchant info
        DB::table('merchant_info')->insert([
            [
                'merchant_id' => 1, 'merchant_name' => 'Demo Merchant',
                'marchant_image' => 'default.jpg', 'merchant_company' => 'Demo Company',
                'merchant_address' => '123 Demo Street', 'machine_number' => 1,
                'alert_new' => 0, 'merchant_request_date' => '2025-03-31 07:03:20',
                'merchant_active_date' => '2025-03-31 07:03:20'
            ]
        ]);

        // Insert machine
        DB::table('machine')->insert([
            [
                'id' => 1, 'name' => 'Demo Machine 1',
                'model' => 'Vending-001', 'code' => 'VENDING-001',
                'device_id' => '123456',
                'number_tray' => 5, 'date_added' => '2025-03-31',
                'machine_system_info' => 'Demo machine system info',
                'machine_note' => 'Demo machine note', 'status' => 1,
                'status_connecting' => 1, 'created_at' => '2025-03-31 07:03:20',
                'created_by' => 1, 'updated_at' => '2025-03-31 07:03:20',
                'updated_by' => 1, 'is_deleted' => 0, 'merchant_id' => 1
            ]
        ]);

        // Insert log status machine
        DB::table('log_status_machine')->insert([
            [
                'id' => 1, 'machine_id' => 1, 'status' => 1,
                'created_at' => '2025-03-31 07:03:20',
                'updated_at' => '2025-03-31 07:03:20'
            ]
        ]);

        // Insert machine attribute
        DB::table('machine_attribute')->insert([
            [
                'id' => 1, 'attribute_name' => 'Location', 'value_default' => 'Ground Floor',
                'created_at' => '2025-03-31 07:03:20', 'created_by' => 1,
                'updated_at' => '2025-03-31 07:03:20', 'updated_by' => 1,
                'is_deleted' => 0
            ]
        ]);

        // Insert machine attribute value
        DB::table('machine_attribute_value')->insert([
            [
                'id' => 1, 'machine_id' => 1, 'attribute_id' => 1,
                'attribute_value' => 'Ground Floor', 'created_at' => '2025-03-31 07:03:20',
                'created_by' => 1, 'updated_at' => '2025-03-31 07:03:20',
                'updated_by' => 1, 'is_deleted' => 0
            ]
        ]);

        // Insert machine merchant mapping
        DB::table('machine_merchant_mapping')->insert([
            [
                'id' => 1, 'merchant_id' => 1, 'machine_id' => 1,
                'created_at' => '2025-03-31 07:03:20', 'created_by' => 1
            ]
        ]);

        // Insert product
        DB::table('product')->insert([
            [
                'id' => 1, 'merchant_id' => 1, 'name' => 'Demo Product 1',
                'price_default' => 10000, 'image' => 'default.jpg',
                'brief' => 'A demo product', 'created_at' => '2025-03-31 07:03:20',
                'created_by' => 1, 'updated_at' => '2025-03-31 07:03:20',
                'updated_by' => 1, 'is_deleted' => 0
            ]
        ]);

        // Insert product list
        DB::table('product_list')->insert([
            [
                'id' => 1, 'merchant_id' => 1, 'machine_id' => 1,
                'product_id' => 1, 'product_price' => 10000,
                'product_item_number' => 100, 'product_item_current' => 100,
                'created_at' => '2025-03-31 07:03:20', 'created_by' => 1,
                'updated_at' => '2025-03-31 07:03:20', 'updated_by' => 1,
                'updated_last_count' => '2025-03-31 07:03:20', 'is_deleted' => 0
            ]
        ]);

        // Insert subscription
        DB::table('subscription')->insert([
            [
                'id' => 1, 'merchant_id' => 1, 'machine_id' => 1,
                'date_expiration' => '2026-03-31', 'created_at' => '2025-03-31 07:03:20',
                'created_by' => 1, 'updated_at' => '2025-03-31 07:03:20',
                'updated_by' => 1, 'checksum' => '53b448e92bd6872644ab7567bf271df1'
            ]
        ]);

        // Insert userF
        DB::table('user')->insert([
            [
                'id' => 1, 'email' => 'user1@example.com',
                'password' => Hash::make('11111111'),
                'coin' => 0, 'merchant_id' => 1, 'is_credit_account' => 0,
                'status' => 1, 'created_at' => '2025-03-31 07:03:20',
                'updated_at' => '2025-03-31 07:03:20', 'is_deleted' => 0
            ]
        ]);

        // Run RoleSeeder
        $this->call([RoleSeeder::class, RoleMerchantSeeder::class]);
    }
}
