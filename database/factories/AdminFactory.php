<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Admin;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Admin::class, function (Faker $faker) {
    return [
        'account'        => $faker->unique()->safeEmail,
        'name'           => $faker->name,
        'email'          => $faker->unique()->safeEmail,
        'password'       => '$2y$10$oQRA2PQ8PW4d.1FZ2ktCgeQvRHfrRZd9yV4rxgNoWHgWXZoi9sUVS', // 11111111
        'remember_token' => Str::random(10),
        'status'         => $faker->numberBetween(0, 1),
        'created_by'     => 1,
    ];
});
