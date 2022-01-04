<?php

use App\Teams;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            TeamSeeder::class,
            UserSeeder::class,
            RoleSeeder::class
            // ProjectSeeder::class
        ]);
    }
}
