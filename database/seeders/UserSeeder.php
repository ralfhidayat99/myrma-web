<?php


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UsersSeeder extends Seeder
{
    public function run()
    {
        // Hapus data user yang ada di tabel sebelumnya
        DB::table('users')->truncate();

        // Generate user admin
        User::create([
            'name' => 'Indra Gunawan',
            'is_admin'  => 1,
            'jabatan' => 'Finance Manager',
            'departemen' => 'IT',
            'id_atasan' => 0,
            'username' => 'indra',
            'password' => Hash::make('123'),
        ]);
        User::create([
            'name' => 'Romi',
            'is_admin'  => 0,
            'jabatan' => 'hokage',
            'departemen' => 'IT',
            'id_atasan' => 1,
            'username' => 'romi',
            'password' => Hash::make('123'),
        ]);
    }
}
