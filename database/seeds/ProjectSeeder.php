<?php

use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
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

        factory(App\Teams::class, 20)->create();

        $users = App\User::all();
        App\Projects::all()->each(function ($projects) use ($users) {
            $projects->users()->attach(rand(1,100));
            $projects->teams()->attach(rand(1,100));
        });


        // factory(App\Projects::class,30)->create()->each(function ($project) {

        //     $project->users()->attach(
        //         $users->random(rand(1, $users->count()))->pluck('user_id')->toArray()
        //     );
        //     $project->teams();
        // });
    }
}
