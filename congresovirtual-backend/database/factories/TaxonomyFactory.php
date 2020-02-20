<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Taxonomy;
use Faker\Generator as Faker;

$factory->define(Taxonomy::class, function (Faker $faker) {
    return [
        'value' => $faker->text(15)
    ];
});
