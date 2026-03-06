<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('root'),
                'role' => 'admin',
                'is_active' => true
            ]
        );

        User::firstOrCreate(
            ['email' => 'ian@gmail.com'],
            [
                'name' => 'Ian',
                'password' => Hash::make('12345678'),
                'role' => 'gerente',
                'is_active' => true
            ]
        );

        User::firstOrCreate(
            ['email' => 'jesus@gmail.com'],
            [
                'name' => 'Jesus',
                'password' => Hash::make('12345678'),
                'role' => 'vendedor',
                'is_active' => true
            ]
        );
    }
}
