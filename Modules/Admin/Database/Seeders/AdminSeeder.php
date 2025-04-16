<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin')->insert([
            'email' => 'admin@example.com',
            'account' => 'admin',
            'password' => Hash::make('admin123'),
            'is_required_change_password' => 1,
            'created_by' => 1,
            'is_deleted' => 0,
            'status' => 1,
            'name' => 'System Administrator',
            'created_at' => now(),
        ]);
    }
} 