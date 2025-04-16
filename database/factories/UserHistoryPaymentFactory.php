<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserHistoryPayment;
use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(UserHistoryPayment::class, function (Faker $faker) {
    return [
        'user_id'                => $faker->numberBetween(1, 31),
        'transaction_type'       => $faker->numberBetween(1, 3),
        'transaction_coin'       => $faker->numberBetween(10, 1000),
        'transaction_device'     => 'PC',
        'transaction_ip_address' => '192.168.1.1',
        'created_at'             => now(),
        'updated_at'             => now(),
        'checksum'               => md5(Str::random(10)),
    ];
});
