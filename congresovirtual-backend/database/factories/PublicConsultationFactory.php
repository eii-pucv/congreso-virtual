<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\PublicConsultation;
use Faker\Generator as Faker;

$factory->define(PublicConsultation::class, function (Faker $faker) {
    return [
        'titulo' => $faker->text(200),
        'autor' => $faker->name(),
        'detalle' => $faker->realText(3000),
        'resumen' => $faker->realtext(255),
        'fecha_inicio' => $faker->dateTime(),
        'fecha_termino' => $faker->dateTime()
    ];
});
