<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/


$factory->define(User::class, function (Faker $faker) {
    $teams = DB::table('teams')->get();
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->freeEmail,
        'email_verified_at' => now(),
        'password' => Hash::make('123123123'), // password
        'remember_token' => Str::random(10),
        'phone' => '034'.rand(100000,999999),
        'age' => rand(22,45),
        'team_id' => rand(0,20),
        'address' => $faker->address
    ];
});
