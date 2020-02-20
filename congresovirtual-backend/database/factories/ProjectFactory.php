<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Project;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Project::class, function (Faker $faker) {
    return [
        'titulo' => $faker->text(200),
        'postulante' => $faker->name(),
        'detalle' => $faker->realText(3000),
        'resumen' => $faker->realtext(255),
        'fecha_inicio' => $faker->dateTime(),
        'fecha_termino' => $faker->dateTime(),
        'boletin' => $faker->shuffleString(10),
        'video' => null
    ];
});
