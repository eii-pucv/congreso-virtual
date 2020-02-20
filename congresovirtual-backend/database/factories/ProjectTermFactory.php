<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\ProjectTerm;
use App\Project;
use App\Term;
use Faker\Generator as Faker;

$factory->define(ProjectTerm::class, function (Faker $faker) {
    return [
        'project_id' => $faker->randomElement(Project::all()),
        'term_id' => $faker->randomElement(Term::all())
    ];
});
