<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name'      => 'Administrator',
            'email'     => 'admin@kafe.com',
            'no_telepon' => null,
            'password'  => Hash::make('password'),
            'role'      => 'admin',
            'is_active' => true,
        ]);

        User::create([
            'name'      => 'Barista Satu',
            'email'     => 'barista@kafe.com',
            'no_telepon' => null,
            'password'  => Hash::make('password'),
            'role'      => 'barista',
            'is_active' => true,
        ]);
    }
}
