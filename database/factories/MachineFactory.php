<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Machine;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Machine::class, function (Faker $faker) {
    return [
        'name'                => $faker->name,
        'model'               => $faker->unique()->phoneNumber,
        'date_added'          => now(),
        'machine_system_info' => $faker->paragraph,
        'machine_note'        => $faker->paragraph,
        'status'              => 1,
        'status_connecting'   => 1,
        'created_at'          => now(),
        'is_deleted'          => 0,
    ];
});
