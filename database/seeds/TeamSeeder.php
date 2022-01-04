<?php

use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Teams::class, 20)->create();

        // factory(App\Projects::class,30)->create();

        $projects = App\Projects::all();

        App\Teams::all()->each(function ($teams) use ($projects) {
            $teams->projects()->attach(rand(1,30));
        });
    }
}
