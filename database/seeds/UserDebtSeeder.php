<?php


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserDebtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_debt')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'debt' => 1000,
                'status' => 0,
                'is_deleted' => 0,
                'is_locked' => 0,
                'created_at' => '2025-03-31 07:03:20',
                'updated_at' => '2025-03-31 07:03:20'
            ],
            [
                'id' => 2,
                'user_id' => 2,
                'debt' => 2000,
                'status' => 0,
                'is_deleted' => 0,
                'is_locked' => 0,
                'created_at' => '2025-03-31 07:03:20',
                'updated_at' => '2025-03-31 07:03:20'
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'debt' => 3000,
                'status' => 0,
                'is_deleted' => 0,
                'is_locked' => 0,
                'created_at' => '2025-03-31 07:03:20',
                'updated_at' => '2025-03-31 07:03:20'
            ]
        ]);
    }
}
