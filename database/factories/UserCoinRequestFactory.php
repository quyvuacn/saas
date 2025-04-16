<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\UserCoinRequest;
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

$factory->define(UserCoinRequest::class, function (Faker $faker) {
    return [
        'user_id'    => $faker->numberBetween(1, 31),
        'coin'       => $faker->numberBetween(1, 10000),
        'message'    => $faker->title,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});
