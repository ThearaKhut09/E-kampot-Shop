<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminSystemController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'role:admin']);
    }

    /**
     * Display system status page
     */
    public function index()
    {
        $status = $this->getSystemStatus();
        return view('admin.system', compact('status'));
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        try {
            // Clear various caches
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            // Clear compiled services and packages
            if (file_exists(base_path('bootstrap/cache/services.php'))) {
                unlink(base_path('bootstrap/cache/services.php'));
            }
            if (file_exists(base_path('bootstrap/cache/packages.php'))) {
                unlink(base_path('bootstrap/cache/packages.php'));
            }

            return response()->json([
                'success' => true,
                'message' => 'All caches cleared successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Cache clear failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Optimize database
     */
    public function optimizeDatabase()
    {
        try {
            // For SQLite, we'll run VACUUM and ANALYZE
            DB::statement('VACUUM');
            DB::statement('ANALYZE');

            // Clear query cache
            DB::statement('PRAGMA cache_size = 10000');

            return response()->json([
                'success' => true,
                'message' => 'Database optimized successfully!'
            ]);
        } catch (\Exception $e) {
            Log::error('Database optimization failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize database: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create backup
     */
    public function backup()
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupPath = storage_path("app/backups/backup_{$timestamp}.sqlite");

            // Create backups directory if it doesn't exist
            if (!File::exists(storage_path('app/backups'))) {
                File::makeDirectory(storage_path('app/backups'), 0755, true);
            }

            // Copy database file
            $dbPath = database_path('database.sqlite');
            if (File::exists($dbPath)) {
                File::copy($dbPath, $backupPath);

                // Also backup storage files if needed
                $storageBackupPath = storage_path("app/backups/storage_{$timestamp}.zip");
                $this->createStorageBackup($storageBackupPath);

                return response()->json([
                    'success' => true,
                    'message' => "Backup created successfully! Files: backup_{$timestamp}.sqlite, storage_{$timestamp}.zip"
                ]);
            } else {
                throw new \Exception('Database file not found');
            }
        } catch (\Exception $e) {
            Log::error('Backup creation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle maintenance mode
     */
    public function maintenance()
    {
        try {
            $isDown = app()->isDownForMaintenance();

            if ($isDown) {
                Artisan::call('up');
                $message = 'Application is now live!';
                $status = 'up';
            } else {
                Artisan::call('down', [
                    '--secret' => 'admin-secret-key',
                    '--render' => 'errors::503'
                ]);
                $message = 'Application is now in maintenance mode. Use /admin-secret-key to access.';
                $status = 'down';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            Log::error('Maintenance mode toggle failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle maintenance mode: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive system status
     */
    private function getSystemStatus()
    {
        return [
            'app' => $this->getAppStatus(),
            'database' => $this->getDatabaseStatus(),
            'storage' => $this->getStorageStatus(),
            'cache' => $this->getCacheStatus(),
            'queue' => $this->getQueueStatus(),
            'security' => $this->getSecurityStatus(),
            'logs' => $this->getLogsStatus()
        ];
    }

    /**
     * Get application status
     */
    private function getAppStatus()
    {
        return [
            'name' => config('app.name'),
            'version' => app()->version(),
            'environment' => config('app.env'),
            'debug' => config('app.debug'),
            'url' => config('app.url'),
            'timezone' => config('app.timezone'),
            'maintenance' => app()->isDownForMaintenance(),
            'uptime' => $this->getUptime()
        ];
    }

    /**
     * Get database status
     */
    private function getDatabaseStatus()
    {
        try {
            $connection = DB::connection();
            $dbPath = database_path('database.sqlite');

            return [
                'status' => 'connected',
                'driver' => $connection->getDriverName(),
                'database' => basename($dbPath),
                'size' => File::exists($dbPath) ? $this->formatBytes(File::size($dbPath)) : 'N/A',
                'tables' => collect(DB::select("SELECT name FROM sqlite_master WHERE type='table'"))->count(),
                'last_backup' => $this->getLastBackupTime()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get storage status
     */
    private function getStorageStatus()
    {
        $storagePath = storage_path();
        $publicPath = public_path();

        return [
            'storage_writable' => is_writable($storagePath),
            'public_writable' => is_writable($publicPath),
            'storage_size' => $this->formatBytes($this->getDirectorySize($storagePath)),
            'public_size' => $this->formatBytes($this->getDirectorySize($publicPath)),
            'free_space' => $this->formatBytes(disk_free_space($storagePath)),
            'temp_files' => count(glob(storage_path('framework/cache/data/*')))
        ];
    }

    /**
     * Get cache status
     */
    private function getCacheStatus()
    {
        return [
            'driver' => config('cache.default'),
            'config_cached' => file_exists(base_path('bootstrap/cache/config.php')),
            'routes_cached' => file_exists(base_path('bootstrap/cache/routes-v7.php')),
            'views_cached' => count(glob(storage_path('framework/views/*'))) > 0,
            'cache_size' => $this->formatBytes($this->getDirectorySize(storage_path('framework/cache')))
        ];
    }

    /**
     * Get queue status
     */
    private function getQueueStatus()
    {
        try {
            return [
                'driver' => config('queue.default'),
                'status' => 'running', // This would need actual worker monitoring
                'pending' => 0, // Would need to query actual queue tables
                'failed' => 0,  // Would need to query failed_jobs table
                'workers' => 1  // Would need actual worker monitoring
            ];
        } catch (\Exception $e) {
            return [
                'driver' => config('queue.default'),
                'status' => 'error',
                'pending' => 0,
                'failed' => 0,
                'workers' => 0
            ];
        }
    }

    /**
     * Get security status
     */
    private function getSecurityStatus()
    {
        return [
            'https' => request()->isSecure(),
            'csrf_protection' => config('app.env') === 'production',
            'debug_mode' => config('app.debug'),
            'error_reporting' => ini_get('display_errors') == '1',
            'session_secure' => config('session.secure'),
            'app_key_set' => !empty(config('app.key'))
        ];
    }

    /**
     * Get logs status
     */
    private function getLogsStatus()
    {
        $logPath = storage_path('logs');
        $logFiles = File::glob($logPath . '/*.log');

        $totalSize = 0;
        $latestLog = null;
        $errorCount = 0;

        foreach ($logFiles as $file) {
            $size = File::size($file);
            $totalSize += $size;

            if (!$latestLog || File::lastModified($file) > File::lastModified($latestLog)) {
                $latestLog = $file;
            }

            // Count errors in latest log
            if ($file === $latestLog) {
                $content = File::get($file);
                $errorCount = substr_count(strtolower($content), 'error');
            }
        }

        return [
            'total_files' => count($logFiles),
            'total_size' => $this->formatBytes($totalSize),
            'latest_log' => $latestLog ? basename($latestLog) : 'None',
            'error_count' => $errorCount,
            'last_error' => $this->getLastError()
        ];
    }

    /**
     * Helper methods
     */
    private function getUptime()
    {
        // Simple uptime calculation based on Laravel installation
        return 'N/A'; // Would need actual server uptime monitoring
    }

    private function getLastBackupTime()
    {
        $backupPath = storage_path('app/backups');
        if (!File::exists($backupPath)) {
            return 'Never';
        }

        $files = File::glob($backupPath . '/backup_*.sqlite');
        if (empty($files)) {
            return 'Never';
        }

        $latest = array_reduce($files, function ($latest, $file) {
            return !$latest || File::lastModified($file) > File::lastModified($latest) ? $file : $latest;
        });

        return Carbon::createFromTimestamp(File::lastModified($latest))->diffForHumans();
    }

    private function getDirectorySize($directory)
    {
        $size = 0;
        if (File::isDirectory($directory)) {
            foreach (File::allFiles($directory) as $file) {
                $size += $file->getSize();
            }
        }
        return $size;
    }

    private function formatBytes($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size >= 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . ' ' . $units[$i];
    }

    private function getLastError()
    {
        $logPath = storage_path('logs');
        $logFiles = File::glob($logPath . '/*.log');

        if (empty($logFiles)) {
            return 'None';
        }

        $latestLog = array_reduce($logFiles, function ($latest, $file) {
            return !$latest || File::lastModified($file) > File::lastModified($latest) ? $file : $latest;
        });

        $content = File::get($latestLog);
        $lines = explode("\n", $content);

        // Find last error line
        for ($i = count($lines) - 1; $i >= 0; $i--) {
            if (stripos($lines[$i], 'error') !== false) {
                return trim($lines[$i]);
            }
        }

        return 'None';
    }

    private function createStorageBackup($zipPath)
    {
        // Simple storage backup - you might want to use a more robust solution
        $zip = new \ZipArchive();
        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {
            $this->addDirectoryToZip($zip, storage_path('app/public'), 'public/');
            $zip->close();
        }
    }

    private function addDirectoryToZip($zip, $directory, $localPath = '')
    {
        if (File::isDirectory($directory)) {
            foreach (File::allFiles($directory) as $file) {
                $relativePath = $localPath . $file->getRelativePathname();
                $zip->addFile($file->getRealPath(), $relativePath);
            }
        }
    }
}
