<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Crear usuario admin si no existe
        $exists = DB::table('users')->where('email', 'admin@gmail.com')->exists();
        
        if (!$exists) {
            DB::table('users')->insert([
                'name' => 'Administrador',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('root'),
                'role' => 'admin',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('users')->where('email', 'admin@gmail.com')->delete();
    }
};
