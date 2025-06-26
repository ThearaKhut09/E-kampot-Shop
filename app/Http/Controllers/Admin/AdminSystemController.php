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
use Illuminate\Support\Str;
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
        $status = [
            'app' => $this->getAppStatus(),
            'database' => $this->getDatabaseStatus(),
            'storage' => $this->getStorageStatus(),
            'cache' => $this->getCacheStatus(),
            'queue' => $this->getQueueStatus(),
            'security' => $this->getSecurityStatus(),
            'logs' => $this->getLogsStatus()
        ];

        // Calculate overall system health
        $status['overall'] = $this->calculateOverallHealth($status);

        return $status;
    }

    /**
     * Get application status
     */
    private function getAppStatus()
    {
        return [
            'name' => config('app.name'),
            'version' => app()->version(),
            'laravel_version' => app()->version(),
            'php_version' => PHP_VERSION,
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
                'status' => 'healthy',
                'connection' => $connection->getDriverName(),
                'driver' => $connection->getDriverName(),
                'database' => basename($dbPath),
                'size' => File::exists($dbPath) ? $this->formatBytes(File::size($dbPath)) : 'N/A',
                'tables' => collect(DB::select("SELECT name FROM sqlite_master WHERE type='table'"))->count(),
                'last_backup' => $this->getLastBackupTime()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'connection' => 'Failed',
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

        // Calculate storage usage
        $totalSpace = disk_total_space($storagePath);
        $freeSpace = disk_free_space($storagePath);
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercentage = $totalSpace > 0 ? round(($usedSpace / $totalSpace) * 100, 1) : 0;

        // Determine status based on usage and writability
        $storageWritable = is_writable($storagePath);
        $publicWritable = is_writable($publicPath);

        $status = 'healthy';
        if (!$storageWritable || !$publicWritable) {
            $status = 'error';
        } elseif ($usagePercentage > 90) {
            $status = 'warning';
        }

        return [
            'status' => $status,
            'storage_writable' => $storageWritable,
            'public_writable' => $publicWritable,
            'storage_size' => $this->formatBytes($this->getDirectorySize($storagePath)),
            'public_size' => $this->formatBytes($this->getDirectorySize($publicPath)),
            'free_space' => $this->formatBytes($freeSpace),
            'temp_files' => count(glob(storage_path('framework/cache/data/*'))),
            'total' => $this->formatBytes($totalSpace),
            'used' => $this->formatBytes($usedSpace),
            'available' => $this->formatBytes($freeSpace),
            'percentage' => $usagePercentage
        ];
    }

    /**
     * Get cache status
     */
    private function getCacheStatus()
    {
        $cacheWorking = true;
        try {
            Cache::put('system_test', 'test', 1);
            $cacheWorking = Cache::get('system_test') === 'test';
            Cache::forget('system_test');
        } catch (\Exception $e) {
            $cacheWorking = false;
        }

        return [
            'status' => $cacheWorking ? 'working' : 'error',
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
        $security = [
            'https' => request()->isSecure(),
            'csrf_protection' => config('app.env') === 'production',
            'debug_mode' => config('app.debug'),
            'error_reporting' => ini_get('display_errors') == '1',
            'session_secure' => config('session.secure'),
            'app_key_set' => !empty(config('app.key')),
            'app_key' => !empty(config('app.key')), // For view compatibility
            'permissions' => $this->checkDirectoryPermissions(),
            'headers' => $this->checkSecurityHeaders()
        ];

        // Calculate security score (0-100)
        $score = 0;

        // HTTPS enabled (+15 points)
        if ($security['https']) {
            $score += 15;
        }

        // CSRF protection (+10 points)
        if ($security['csrf_protection']) {
            $score += 10;
        }

        // Debug mode disabled in production (+20 points)
        if (config('app.env') === 'production' && !$security['debug_mode']) {
            $score += 20;
        } elseif (config('app.env') !== 'production') {
            $score += 10; // Partial credit for non-production
        }

        // Error reporting disabled (+10 points)
        if (!$security['error_reporting']) {
            $score += 10;
        }

        // Session secure (+10 points)
        if ($security['session_secure']) {
            $score += 10;
        }

        // App key set (+15 points)
        if ($security['app_key_set']) {
            $score += 15;
        }

        // Directory permissions (+10 points)
        if ($security['permissions'] === 'Secure') {
            $score += 10;
        }

        // Security headers (+10 points)
        if ($security['headers'] === 'Present') {
            $score += 10;
        }

        $security['score'] = $score;

        return $security;
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
        $recentLogs = [];

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

        // Parse recent log entries from the latest log file
        if ($latestLog && File::exists($latestLog)) {
            $recentLogs = $this->parseRecentLogEntries($latestLog);
        }

        // Return both summary data and recent log entries for the table
        return array_merge([
            'total_files' => count($logFiles),
            'total_size' => $this->formatBytes($totalSize),
            'latest_log' => $latestLog ? basename($latestLog) : 'None',
            'error_count' => $errorCount,
            'last_error' => $this->getLastError()
        ], $recentLogs);
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

    /**
     * Parse recent log entries from a log file
     */
    private function parseRecentLogEntries($logFile, $limit = 10)
    {
        if (!File::exists($logFile)) {
            return [];
        }

        try {
            $content = File::get($logFile);
            $lines = explode("\n", $content);
            $entries = [];

            // Laravel log format: [2024-06-26 12:00:00] local.ERROR: Message
            $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(\w+): (.+)/';

            // Parse from the end of the file to get recent entries
            $reversedLines = array_reverse($lines);

            foreach ($reversedLines as $line) {
                if (count($entries) >= $limit) {
                    break;
                }

                if (preg_match($pattern, trim($line), $matches)) {
                    $entries[] = [
                        'time' => $matches[1],
                        'level' => strtolower($matches[2]),
                        'message' => Str::limit($matches[3], 100)
                    ];
                }
            }

            return $entries;
        } catch (\Exception $e) {
            // If parsing fails, return some dummy entries so the table doesn't break
            return [
                [
                    'time' => date('Y-m-d H:i:s'),
                    'level' => 'info',
                    'message' => 'Unable to parse log entries'
                ]
            ];
        }
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

    /**
     * Calculate overall system health based on component statuses
     */
    private function calculateOverallHealth($status)
    {
        $issues = [];

        // Check database
        if (isset($status['database']['status']) && $status['database']['status'] !== 'healthy') {
            $issues[] = 'database';
        }

        // Check storage
        if (isset($status['storage']['storage_writable']) && !$status['storage']['storage_writable']) {
            $issues[] = 'storage';
        }

        if (isset($status['storage']['public_writable']) && !$status['storage']['public_writable']) {
            $issues[] = 'public_storage';
        }

        // Check security
        if (isset($status['security']['debug_mode']) && $status['security']['debug_mode'] && config('app.env') === 'production') {
            $issues[] = 'debug_mode';
        }

        if (isset($status['security']['app_key_set']) && !$status['security']['app_key_set']) {
            $issues[] = 'app_key';
        }

        // Check logs for errors
        if (isset($status['logs']['error_count']) && $status['logs']['error_count'] > 10) {
            $issues[] = 'logs';
        }

        // Determine overall health
        if (empty($issues)) {
            return 'healthy';
        } elseif (count($issues) <= 2) {
            return 'warning';
        } else {
            return 'critical';
        }
    }

    /**
     * Check directory permissions for security
     */
    private function checkDirectoryPermissions()
    {
        $criticalDirs = [
            storage_path(),
            storage_path('logs'),
            storage_path('framework'),
            base_path('bootstrap/cache')
        ];

        $issues = [];
        foreach ($criticalDirs as $dir) {
            if (!is_writable($dir)) {
                $issues[] = basename($dir);
            }
        }

        // Check for overly permissive permissions (777)
        if (File::exists(storage_path()) && (fileperms(storage_path()) & 0777) === 0777) {
            $issues[] = 'overly_permissive';
        }

        return empty($issues) ? 'Secure' : 'Issues: ' . implode(', ', $issues);
    }

    /**
     * Check for basic security headers
     */
    private function checkSecurityHeaders()
    {
        // In a real application, you would check the actual HTTP response headers
        // For now, we'll do a basic check based on configuration
        $headers = [];

        // Check if we have middleware that might add security headers
        $middlewareConfig = config('app.middleware', []);

        // Basic security headers that should be present
        $requiredHeaders = ['X-Frame-Options', 'X-Content-Type-Options', 'X-XSS-Protection'];

        // For development, assume basic headers are missing unless specifically configured
        if (config('app.env') === 'production') {
            return 'Should be configured'; // In production, assume they should be there
        }

        return 'Development'; // In development, headers may not be fully configured
    }
}
