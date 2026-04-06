<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\BackupRecord;

class BackupController extends Controller
{
    /**
     * Obtener ruta de backups (Railway Volume o storage local)
     */
    private function getBackupsPath(): string
    {
        // Railway Volume tiene prioridad si está configurado
        $volumePath = env('RAILWAY_VOLUME_MOUNT_PATH');
        
        if ($volumePath && is_dir($volumePath)) {
            $backupDir = rtrim($volumePath, '/') . '/backups';
            if (!is_dir($backupDir)) {
                @mkdir($backupDir, 0755, true);
            }
            return $backupDir;
        }
        
        // Fallback a storage local
        return storage_path('backups');
    }

    /**
     * Mostrar página de gestión de backups
     */
    public function index()
    {
        $backups = $this->getBackupsList();
        $totalSize = $this->calculateTotalSize();
        $autoBackupEnabled = app_setting('auto_backup_enabled', '0') === '1';
        $retentionDays = (int) app_setting('backup_retention_days', '30');
        $backupPath = app_setting('backup_local_path', 'D:\\Backups\\Paleteria');
        
        // Info de Railway Volume
        $volumePath = env('RAILWAY_VOLUME_MOUNT_PATH');
        $usingVolume = $volumePath && is_dir($volumePath);
        $serverBackupPath = $this->getBackupsPath();
        
        // Historial de backups de la base de datos
        $backupHistory = BackupRecord::with('creator')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return view('panel.backups', compact(
            'backups',
            'totalSize',
            'autoBackupEnabled',
            'retentionDays',
            'backupPath',
            'backupHistory',
            'usingVolume',
            'serverBackupPath'
        ));
    }

    /**
     * Crear nuevo backup y descargarlo automáticamente
     */
    public function create()
    {
        try {
            // Usar Railway Volume si está disponible
            $dir = $this->getBackupsPath();
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            // Ejecutar comando de backup
            $exitCode = Artisan::call('backup:db');
            
            if ($exitCode !== 0) {
                return back()->with('error', 'Error al crear backup. Revisa los logs del sistema.');
            }

            // Buscar el archivo más reciente
            $files = glob($dir . DIRECTORY_SEPARATOR . '*.sql');
            if (!empty($files)) {
                usort($files, fn($a, $b) => filemtime($b) - filemtime($a));
                $latestFile = $files[0];
                $filename = basename($latestFile);
                $fileSize = filesize($latestFile);
                
                // Guardar registro en base de datos (historial permanente)
                $record = BackupRecord::create([
                    'filename' => $filename,
                    'size_bytes' => $fileSize,
                    'created_by' => auth()->id(),
                    'downloaded' => true, // Se marca como descargado porque se descarga automáticamente
                    'downloaded_at' => now(),
                ]);
                
                // Registrar en auditoría
                AuditLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'backup.created',
                    'module' => 'sistema',
                    'entity_type' => 'Backup',
                    'entity_id' => $record->id,
                    'meta' => ['_entity_name' => $filename, 'size' => $fileSize],
                ]);
                
                // Descargar automáticamente
                return response()->download($latestFile, $filename, [
                    'Content-Type' => 'application/sql',
                ])->deleteFileAfterSend(false);
            }
            
            return back()->with('error', 'El backup se creó pero no se encontró el archivo.');
        } catch (\Exception $e) {
            Log::error('Error creando backup: ' . $e->getMessage());
            return back()->with('error', 'Error al crear backup: ' . $e->getMessage());
        }
    }

    /**
     * Descargar backup existente
     */
    public function download(string $filename)
    {
        $filename = basename($filename);
        $path = $this->getBackupsPath() . '/' . $filename;

        if (!file_exists($path) || !str_ends_with($filename, '.sql')) {
            abort(404, 'Backup no encontrado en el servidor. Puede que ya se haya eliminado con un deploy.');
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'backup.downloaded',
            'module' => 'sistema',
            'entity_type' => 'Backup',
            'entity_id' => null,
            'meta' => ['_entity_name' => $filename, 'size' => filesize($path)],
        ]);

        return response()->download($path);
    }

    /**
     * Eliminar backup
     */
    public function delete(string $filename)
    {
        $filename = basename($filename);
        $path = $this->getBackupsPath() . '/' . $filename;

        if (!file_exists($path) || !str_ends_with($filename, '.sql')) {
            return back()->with('error', 'Backup no encontrado.');
        }

        $size = filesize($path);
        unlink($path);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'backup.deleted',
            'module' => 'sistema',
            'entity_type' => 'Backup',
            'entity_id' => null,
            'meta' => ['_entity_name' => $filename, 'size' => $size],
        ]);

        return back()->with('success', "Backup eliminado: {$filename}");
    }

    /**
     * Guardar configuración de backups
     */
    public function saveConfig(Request $request)
    {
        $data = $request->validate([
            'auto_backup_enabled' => ['required', 'boolean'],
            'backup_retention_days' => ['required', 'integer', 'min:1', 'max:365'],
            'backup_local_path' => ['nullable', 'string', 'max:500'],
        ]);

        Setting::set('auto_backup_enabled', $data['auto_backup_enabled'] ? '1' : '0');
        Setting::set('backup_retention_days', (string) $data['backup_retention_days']);
        Setting::set('backup_local_path', $data['backup_local_path'] ?? 'D:\\Backups\\Paleteria');

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'backup.config_updated',
            'module' => 'sistema',
            'entity_type' => 'Setting',
            'entity_id' => null,
            'meta' => [
                '_entity_name' => 'Configuración de backups',
                'auto_backup' => $data['auto_backup_enabled'] ? 'Activado' : 'Desactivado',
                'retention' => $data['backup_retention_days'] . ' días',
                'ruta_local' => $data['backup_local_path'] ?? 'No configurada',
            ],
        ]);

        return back()->with('success', 'Configuración de backups guardada correctamente.');
    }

    /**
     * Obtener lista de backups
     */
    private function getBackupsList(): array
    {
        $dir = $this->getBackupsPath();
        $backups = [];

        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
            return $backups;
        }

        $files = glob($dir . DIRECTORY_SEPARATOR . '*.sql');
        
        if ($files === false) {
            return $backups;
        }

        foreach ($files as $file) {
            if (!is_file($file)) continue;
            
            $backups[] = [
                'filename' => basename($file),
                'size' => $this->formatBytes(filesize($file)),
                'size_bytes' => filesize($file),
                'date' => date('Y-m-d H:i:s', filemtime($file)),
                'age' => now()->diffForHumans(\Carbon\Carbon::createFromTimestamp(filemtime($file))),
            ];
        }

        usort($backups, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));

        return $backups;
    }

    /**
     * Calcular tamaño total de backups
     */
    private function calculateTotalSize(): string
    {
        $dir = $this->getBackupsPath();
        $totalBytes = 0;

        if (is_dir($dir)) {
            $files = glob($dir . DIRECTORY_SEPARATOR . '*.sql');
            if ($files) {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $totalBytes += filesize($file);
                    }
                }
            }
        }

        return $this->formatBytes($totalBytes);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
