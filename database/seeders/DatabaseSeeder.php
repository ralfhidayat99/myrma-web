<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

// use Database\Seeders\UsersSeeder as SeedersUsersSeeder;

use App\Models\Supervisor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DB::table('users')->truncate();

        User::create([
            'name' => 'Michael Purwanto',
            'is_admin'  => 1,
            'jabatan' => 'General Manager',
            'departemen' => 'General',
            'id_atasan' => 0,
            'username' => 'michael',
            'password' => Hash::make('123'),
        ]);
        User::create([
            'name' => 'Indra Gunawan',
            'is_admin'  => 1,
            'jabatan' => 'Finance Manager',
            'departemen' => 'Finance',
            'id_atasan' => 1,
            'username' => 'indra',
            'password' => Hash::make('123'),
        ]);
        User::create([
            'name' => 'Edy Sutrisno',
            'is_admin'  => 1,
            'jabatan' => 'HRGA Manager',
            'departemen' => 'HRGA',
            'id_atasan' => 1,
            'username' => 'edy',
            'password' => Hash::make('123'),
        ]);
        User::create([
            'name' => 'Romi Alfa H',
            'is_admin'  => 0,
            'jabatan' => 'Programmer',
            'departemen' => 'IT',
            'id_atasan' => 2,
            'username' => 'romi',
            'password' => Hash::make('123'),
        ]);

        Supervisor::create([
            'name' => 'Michael Purwanto',
            'username' => 'michael',
            'password' => Hash::make('123'),
            'departemen' => 'General',
            'lvl' => 1,
        ]);

        // Supervisor::create([
        //     'name' => 'Indra Gunawan',
        //     'username' => 'indra',
        //     'password' => Hash::make('123'),
        //     'departemen' => 'Tax Accounting',
        //     'lvl' => 1,
        // ]);

        // Supervisor::create([
        //     'name' => 'Edy Sutrisno',
        //     'username' => 'edy',
        //     'password' => Hash::make('123'),
        //     'departemen' => 'HRGA',
        //     'lvl' => 1,
        // ]);

        // Supervisor::create([
        //     'name' => 'Andika Dwimawan',
        //     'username' => 'andika',
        //     'password' => Hash::make('123'),
        //     'departemen' => 'Finance',
        //     'lvl' => 1,
        // ]);
    }
}
