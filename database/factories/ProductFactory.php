<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'merchant_id'   => $faker->numberBetween(1, 30),
        'name'          => 'Product-'.$faker->numberBetween(1,30),
        'price_default' => $faker->randomElement(['10000', '5000', '15000']),
        'image'         => $faker->imageUrl(),
        'brief'         => $faker->words,
        'created_by'     => 1,
    ];
});
