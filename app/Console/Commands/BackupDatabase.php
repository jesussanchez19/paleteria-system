<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'backup:db {--path= : Ruta destino del .sql}';
    protected $description = 'Genera un backup SQL de PostgreSQL usando pg_dump';

    public function handle(): int
    {
        $connection = config('database.default');

        if ($connection !== 'pgsql') {
            $this->error('Este comando está pensado para PostgreSQL (pgsql).');
            return self::FAILURE;
        }

        $host = config('database.connections.pgsql.host');
        $port = config('database.connections.pgsql.port');
        $db   = config('database.connections.pgsql.database');
        $user = config('database.connections.pgsql.username');
        $pass = config('database.connections.pgsql.password');

        $dir = storage_path('backups');
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $filename = 'paleteria_backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $defaultPath = $dir . DIRECTORY_SEPARATOR . $filename;

        $path = $this->option('path') ?: $defaultPath;

        // pg_dump debe existir en el PATH del sistema (normal en instalaciones de PostgreSQL)
        // En Windows, si no está, se debe agregar la carpeta bin de PostgreSQL al PATH.
        $cmd = sprintf(
            'pg_dump --host=%s --port=%s --username=%s --format=p %s > %s',
            escapeshellarg((string) $host),
            escapeshellarg((string) $port),
            escapeshellarg((string) $user),
            escapeshellarg((string) $db),
            escapeshellarg((string) $path)
        );

        // Setear contraseña sin exponerla en comando (PGPASSWORD)
        // En Windows cmd/powershell, la sintaxis cambia, por eso usamos putenv:
        putenv('PGPASSWORD=' . $pass);

        $exitCode = 0;
        system($cmd, $exitCode);

        // limpiar variable
        putenv('PGPASSWORD');

        if ($exitCode !== 0) {
            $this->error('Falló pg_dump. Revisa que PostgreSQL esté instalado y pg_dump esté en PATH.');
            return self::FAILURE;
        }

        $this->info('Backup creado: ' . $path);

        // auditoría opcional si ya tienes audit_log()
        if (function_exists('audit_log')) {
            audit_log('backup.db', 'system', null, [
                'path' => $path,
                'db' => $db,
                'host' => $host,
                'port' => $port,
            ]);
        }

        return self::SUCCESS;
    }
}
