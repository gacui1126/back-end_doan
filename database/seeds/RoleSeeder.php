<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            [
                'name' => 'admin',
                'auth_name' => 'Quản trị hệ thống'
            ],
            [
                'name' => 'leader',
                'auth_name' => 'Trưởng nhóm'
            ],
            [
                'name' => 'member',
                'auth_name' => 'nhân viên'
            ],
            [
                'name' => 'manager',
                'auth_name' => 'Quản lý'
            ]
        ]);
    }
}
