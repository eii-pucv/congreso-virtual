<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\TaxonomyTerm;
use App\Taxonomy;
use App\Term;
use Faker\Generator as Faker;

$factory->define(TaxonomyTerm::class, function (Faker $faker) {
    return [
        'taxonomy_id' => $faker->randomElement(Taxonomy::all()),
        'term_id' => $faker->randomElement(Term::all())
    ];
});
