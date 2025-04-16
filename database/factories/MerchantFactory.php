<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Merchant;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Merchant::class, function (Faker $faker) {
    return [
        'account'           => $faker->unique()->safeEmail,
        'name'              => $faker->name,
        'email'             => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'phone'             => strval($faker->phoneNumber),
        'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token'    => Str::random(10),
        'access_token'      => Str::random(10),
        'machine_count'     => $faker->numberBetween(0, 100),
        'parent_id'         => 0,
        'status'            => $faker->numberBetween(0, 2),
    ];
});
