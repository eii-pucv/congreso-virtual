<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\PublicConsultationTerm;
use App\PublicConsultation;
use App\Term;
use Faker\Generator as Faker;

$factory->define(PublicConsultationTerm::class, function (Faker $faker) {
    return [
        'public_consultation_id' => $faker->randomElement(PublicConsultation::all()),
        'term_id' => $faker->randomElement(Term::all())
    ];
});
