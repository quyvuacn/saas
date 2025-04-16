<?php


use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create admin roles
        $adminRoles = [
            [
                'name' => 'Dashboard Manager',
                'alias' => 'dashboard-manager',
                'group' => Role::GROUP_ADMIN,
                'permissions' => ['dashboard', 'dashboard.list']
            ],
            [
                'name' => 'Account Manager',
                'alias' => 'account-manager',
                'group' => Role::GROUP_ADMIN,
                'permissions' => ['account.list', 'account.edit', 'account.change', 'account.create', 'account.show']
            ],
            [
                'name' => 'Permission Manager',
                'alias' => 'permission-manager',
                'group' => Role::GROUP_ADMIN,
                'permissions' => ['permission.list', 'permission.edit', 'permission.change']
            ]
        ];

        // Create merchant roles
        $merchantRoles = [
            [
                'name' => 'Dashboard Manager',
                'alias' => 'dashboard-manager',
                'group' => Role::GROUP_MERCHANT,
                'permissions' => ['dashboard', 'dashboard.list']
            ],
            [
                'name' => 'Customer Manager',
                'alias' => 'customer-manager',
                'group' => Role::GROUP_MERCHANT,
                'permissions' => ['user.list', 'user.edit', 'user.change', 'user.create', 'user.show']
            ],
            [
                'name' => 'Account Manager',
                'alias' => 'account-manager',
                'group' => Role::GROUP_MERCHANT,
                'permissions' => ['account.list', 'account.edit', 'account.change', 'account.create', 'account.show']
            ],
            [
                'name' => 'Machine Manager',
                'alias' => 'machine-manager',
                'group' => Role::GROUP_MERCHANT,
                'permissions' => ['machine.list', 'machine.edit', 'machine.change', 'machine.create', 'machine.show']
            ]
        ];

        // Create all roles (super admin)
        $allRoles = [
            [
                'name' => 'Super Admin',
                'alias' => 'super-admin',
                'group' => Role::GROUP_ALL,
                'permissions' => ['super.admin']
            ]
        ];

        // Insert all roles
        foreach (array_merge($adminRoles, $merchantRoles, $allRoles) as $roleData) {
            $role = Role::create([
                'name' => $roleData['name'],
                'alias' => $roleData['alias'],
                'group' => $roleData['group'],
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
            }
        }
    }
}
