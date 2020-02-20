<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Idea;
use Faker\Generator as Faker;

$factory->define(Idea::class, function (Faker $faker) {
    return [
        'titulo' => $faker->text(150),
        'detalle' => $faker->realtext(255)
    ];
});
