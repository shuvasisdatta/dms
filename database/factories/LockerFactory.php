<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Locker;
use Faker\Generator as Faker;

$factory->define(Locker::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->name
    ];
});
