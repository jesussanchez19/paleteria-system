<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('root'),
            'role' => 'admin',
            'is_active' => true
        ]);

        User::create([
            'name' => 'Ian',
            'email' => 'ian@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'gerente',
            'is_active' => true
        ]);

        User::create([
            'name' => 'Jesus',
            'email' => 'jesus@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'vendedor',
            'is_active' => true
        ]);
    }
}
