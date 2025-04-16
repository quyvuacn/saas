<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Machine;
use App\Models\Subscription;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Subscription::class, function (Faker $faker) {
    return [
        'merchant_id'     => $faker->numberBetween(1, 60),
        'machine_id'      => $faker->numberBetween(1, 60),
        'date_expiration' => now()->addYears($faker->numberBetween(1, 5)),
        'created_at'      => now(),
        'created_by'      => 1,
        'updated_at'      => now(),
        'updated_by'      => 1,
        'checksum'        => Str::random(10),
    ];
});
