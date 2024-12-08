<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'username' => 'admin',
            'role' => 'ADMIN',
        ]);

        User::create([
            'name' => 'Usuario',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'username' => 'user',
            'role' => 'USER',
        ]);

        User::create([
            'name' => 'Luis',
            'email' => 'luis@example.com',
            'password' => bcrypt('password'),
            'username' => 'luis',
            'role' => 'USER',
        ]);

        User::create([
            'name' => 'Mayo',
            'email' => 'mayo@example.com',
            'password' => bcrypt('password'),
            'username' => 'mayo',
            'role' => 'USER',
        ]);

        User::create([
            'name' => 'CoolerLuis',
            'email' => 'carlos@example.com',
            'password' => bcrypt('password'),
            'username' => 'coolerLuis',
            'role' => 'USER',
        ]);

    }
}
