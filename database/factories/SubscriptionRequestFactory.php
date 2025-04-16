<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\SubscriptionRequest;
use Faker\Generator as Faker;

$factory->define(SubscriptionRequest::class, function (Faker $faker) {
    return [
        'merchant_id'           => $faker->numberBetween(1, 30),
        'machine_id'            => $faker->numberBetween(1, 30),
        'date_expiration_begin' => now(),
        'date_expiration_end'   => $faker->dateTimeThisDecade(),
        'created_at'            => now(),
        'updated_at'            => now(),
        'checksum'              => md5($faker->name),
        'created_by'            => 1,
        'code'                  => 'VTI-',
    ];
});
