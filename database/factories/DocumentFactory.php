<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Document;
use Faker\Generator as Faker;

$factory->define(Document::class, function (Faker $faker) {
    return [
        'title'         => $faker->name,
        'description'   => $faker->sentence(6),
        'type'          => $faker->randomElement(['pdf', 'doc', 'xlsx', 'ppt']),
        'slug'          => $faker->unique()->imageUrl
    ];
});
