<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Teams;
use App\Model;
use Faker\Generator as Faker;

$factory->define(Teams::class, function (Faker $faker) {
    return [
        'name' => 'team '.rand(1,100)
    ];
});
