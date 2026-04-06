<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class BackupDatabase extends Command
{
    protected $signature = 'backup:db {--path= : Ruta destino del .sql}';
    protected $description = 'Genera un backup SQL de la base de datos usando PHP puro';

    public function handle(): int
    {
        $dir = $this->getBackupsPath();
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filename = 'paleteria_backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $defaultPath = $dir . DIRECTORY_SEPARATOR . $filename;
        $path = $this->option('path') ?: $defaultPath;

        $this->info('Generando backup en: ' . $dir);
        $this->info('(Railway Volume: ' . (env('RAILWAY_VOLUME_MOUNT_PATH') ? 'Sí' : 'No') . ')');

        try {
            $sql = $this->generateBackupSQL();
            File::put($path, $sql);

            $this->info('Backup creado: ' . $path);
            $this->info('Tamaño: ' . $this->formatBytes(filesize($path)));

            // auditoría
            if (function_exists('audit_log')) {
                audit_log('backup.db', 'system', null, [
                    'path' => $path,
                    'size' => filesize($path),
                ]);
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error al crear backup: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Obtener ruta de backups (Railway Volume o storage local)
     */
    private function getBackupsPath(): string
    {
        $volumePath = env('RAILWAY_VOLUME_MOUNT_PATH');
        
        if ($volumePath && is_dir($volumePath)) {
            return rtrim($volumePath, '/') . '/backups';
        }
        
        return storage_path('backups');
    }

    private function generateBackupSQL(): string
    {
        $sql = "-- Backup generado el " . now()->format('Y-m-d H:i:s') . "\n";
        $sql .= "-- Sistema: Paletería Creamyx\n\n";

        // Tablas a exportar (orden importante por dependencias)
        $tables = [
            'users',
            'settings',
            'products',
            'inventories',
            'sales',
            'sale_details',
            'cash_registers',
            'audit_logs',
            'weather_snapshots',
            'backup_records',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $this->line("Exportando: {$table}");

            // Obtener estructura de columnas
            $columns = Schema::getColumnListing($table);
            $records = DB::table($table)->get();

            if ($records->isEmpty()) {
                $sql .= "-- Tabla '{$table}' está vacía\n\n";
                continue;
            }

            $sql .= "-- Tabla: {$table} ({$records->count()} registros)\n";
            $sql .= "TRUNCATE TABLE \"{$table}\" CASCADE;\n";

            foreach ($records as $record) {
                $values = [];
                foreach ($columns as $column) {
                    $value = $record->{$column};
                    if (is_null($value)) {
                        $values[] = 'NULL';
                    } elseif (is_bool($value)) {
                        $values[] = $value ? 'TRUE' : 'FALSE';
                    } elseif (is_numeric($value) && !is_string($value)) {
                        $values[] = $value;
                    } else {
                        // Escapar comillas simples
                        $escaped = str_replace("'", "''", (string) $value);
                        $values[] = "'" . $escaped . "'";
                    }
                }

                $columnList = '"' . implode('", "', $columns) . '"';
                $valueList = implode(', ', $values);
                $sql .= "INSERT INTO \"{$table}\" ({$columnList}) VALUES ({$valueList});\n";
            }

            $sql .= "\n";
        }

        // Resetear secuencias
        $sql .= "-- Resetear secuencias\n";
        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'id')) {
                $maxId = DB::table($table)->max('id') ?? 0;
                $sql .= "SELECT setval(pg_get_serial_sequence('\"{$table}\"', 'id'), {$maxId}, true);\n";
            }
        }

        return $sql;
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
