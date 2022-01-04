<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 100)->create();

        factory(App\Projects::class,30)->create();

        $projects = App\Projects::all();

        App\User::all()->each(function ($users) use ($projects) {
            $users->projects()->attach(rand(1,100));
        });
    }
}
