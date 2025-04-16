<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleMerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create merchant roles
        $merchantRoles = [
            [
                'name' => 'Dashboard Manager',
                'alias' => 'dashboard-manager',
                'group' => Role::GROUP_MERCHANT,
                'order' => 1,
                'permissions' => ['dashboard', 'dashboard.list']
            ],
            [
                'name' => 'Customer Manager',
                'alias' => 'customer-manager',
                'group' => Role::GROUP_MERCHANT,
                'order' => 2,
                'permissions' => [
                    'user.list', 'user.edit', 'user.change', 'user.create', 'user.show',
                    'user_coin_request.list', 'user_coin_request.edit', 'user_coin_request.change',
                    'user_credit.list', 'user_credit.edit', 'user_credit.change',
                    'user_debt.list', 'user_debt.edit', 'user_debt.change'
                ]
            ],
            [
                'name' => 'Machine Manager',
                'alias' => 'machine-manager',
                'group' => Role::GROUP_MERCHANT,
                'order' => 3,
                'permissions' => [
                    'machine.list', 'machine.edit', 'machine.change', 'machine.create', 'machine.show',
                    'machine_request.list', 'machine_request.edit', 'machine_request.change',
                    'machine_request_history.list', 'machine_request_history.show',
                    'machine_request_back.list', 'machine_request_back.edit', 'machine_request_back.change'
                ]
            ],
            [
                'name' => 'Product Manager',
                'alias' => 'product-manager',
                'group' => Role::GROUP_MERCHANT,
                'order' => 4,
                'permissions' => [
                    'product.list', 'product.edit', 'product.change', 'product.create', 'product.show',
                    'product_selling.list', 'product_selling.edit', 'product_selling.change',
                    'product_sync.list', 'product_sync.edit', 'product_sync.change',
                    'selling_history.list', 'selling_history.show'
                ]
            ],
            [
                'name' => 'Subscription Manager',
                'alias' => 'subscription-manager',
                'group' => Role::GROUP_MERCHANT,
                'order' => 5,
                'permissions' => [
                    'subscription.list', 'subscription.edit', 'subscription.change', 'subscription.create', 'subscription.show',
                    'subscription_history.list', 'subscription_history.show'
                ]
            ],
            [
                'name' => 'Account Manager',
                'alias' => 'account-manager',
                'group' => Role::GROUP_MERCHANT,
                'order' => 6,
                'permissions' => [
                    'account.list', 'account.edit', 'account.change', 'account.create', 'account.show',
                    'account_history.list', 'account_history.show'
                ]
            ],
            [
                'name' => 'Permission Manager',
                'alias' => 'permission-manager',
                'group' => Role::GROUP_MERCHANT,
                'order' => 7,
                'permissions' => [
                    'permission.list', 'permission.edit', 'permission.change'
                ]
            ],
            [
                'name' => 'System Manager',
                'alias' => 'system-manager',
                'group' => Role::GROUP_MERCHANT,
                'order' => 8,
                'permissions' => [
                    'setting.list', 'setting.edit', 'setting.change',
                    'notify.list', 'notify.edit', 'notify.change',
                    'merchant_ads.list', 'merchant_ads.edit', 'merchant_ads.change'
                ]
            ],
            [
                'name' => 'Accountant Manager',
                'alias' => 'accountant-manager',
                'group' => Role::GROUP_MERCHANT,
                'order' => 9,
                'permissions' => [
                    'selling.list', 'selling.edit', 'selling.change',
                    'selling_history.list', 'selling_history.show',
                    'accounting.list', 'accounting.edit', 'accounting.change'
                ]
            ]
        ];

        // Insert merchant roles
        foreach ($merchantRoles as $roleData) {
            $role = Role::create([
                'name' => $roleData['name'],
                'alias' => $roleData['alias'],
                'group' => $roleData['group'],
                'order' => $roleData['order'],
                'status' => Role::IS_ACTIVED,
                'is_deleted' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Get permission IDs for this role
            $permissionIds = Permission::whereIn('permission_name', $roleData['permissions'])
                ->where('is_deleted', '!=', Permission::IS_DELETED)
                ->where('status', Permission::IS_ACTIVED)
                ->pluck('id');

            // Create role_permissions relationships
            foreach ($permissionIds as $permissionId) {
                DB::table('role_permissions')->insert([
                    'role_id' => $role->id,
                    'permission_id' => $permissionId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Add account permissions for account id 1
                DB::table('account_permission')->insert([
                    'account_id' => 1,
                    'permission_id' => $permissionId,
                    'table' => 'merchant',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }
}
