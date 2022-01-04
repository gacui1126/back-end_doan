<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Projects;
use Faker\Generator as Faker;

$factory->define(Projects::class, function (Faker $faker) {
    return [
        'name' => 'project '.rand(1,1000),
        'user_create_id' => rand(1,100),
        'start_at' => '2021-04-22 11:59:23',
        'end_at' => '2022-04-22 11:59:23'
    ];
});
