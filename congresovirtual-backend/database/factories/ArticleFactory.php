<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Article;
use Faker\Generator as Faker;

$factory->define(Article::class, function (Faker $faker) {
    return [
        'titulo' => $faker->text(150),
        'detalle' => $faker->realtext(255)
    ];
});
