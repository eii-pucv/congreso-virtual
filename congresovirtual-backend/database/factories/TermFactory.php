<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Term;
use Faker\Generator as Faker;

$factory->define(Term::class, function (Faker $faker) {
    return [
        'value' => $faker->text(15)
    ];
});
