<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Akun Admin Toko Ananda
    \App\Models\User::create([
        'name' => 'Admin Ananda',
        'email' => 'admin@ananda.site', // Gunakan domain ananda Bapak
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'role' => 'admin',
    ]);

    // Akun Kasir Toko Ananda
    \App\Models\User::create([
        'name' => 'Kasir Ananda',
        'email' => 'kasir@ananda.site',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'role' => 'cashier',
    ]);
    }
}
