<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Valores por defecto para configuraciones críticas del sistema
     */
    protected array $defaults = [
        'maintenance_mode' => '0',
        'sales_enabled' => '1',
        'max_login_attempts' => '5',
        'session_timeout' => '120',
        'force_password_change_days' => '0',
        'max_sale_without_auth' => '500',
        'max_discount_percent' => '15',
        'auto_backup_enabled' => '1',
        'backup_retention_days' => '30',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->defaults as $key => $value) {
            // Solo insertar si no existe
            $exists = DB::table('settings')->where('key', $key)->exists();
            if (!$exists) {
                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No eliminar los settings en rollback para evitar pérdida de configuración
    }
};
