<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Vote;
use App\VoteType;
use App\Element;
use App\User;
use Faker\Generator as Faker;

$factory->define(Vote::class, function (Faker $faker) {
    return [
        //'user_id' => $faker->randomElement(User::all())
    ];
});
