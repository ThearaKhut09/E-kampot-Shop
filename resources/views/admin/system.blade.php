@extends('admin.layout')

@section('title', 'System Status')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">System Status</h2>
        <div class="flex space-x-2">
            <button onclick="location.reload()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                Refresh Status
            </button>
            <a href="{{ route('admin.bulk.index') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                Maintenance Tools
            </a>
        </div>
    </div>

    <!-- Overall System Health -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Overall System Health</h3>
            <div class="flex items-center">
                <div class="w-3 h-3 bg-{{ $status['overall'] === 'healthy' ? 'green' : ($status['overall'] === 'warning' ? 'yellow' : 'red') }}-500 rounded-full mr-2"></div>
                <span class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ $status['overall'] }}</span>
            </div>
        </div>
        @if($status['overall'] !== 'healthy')
            <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                    Some system components require attention. Please review the details below.
                </p>
            </div>
        @endif
    </div>

    <!-- System Components -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Database Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Database</h3>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-{{ $status['database']['status'] === 'healthy' ? 'green' : 'red' }}-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($status['database']['status']) }}</span>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Connection:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['database']['connection'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Tables:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['database']['tables'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Database Size:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['database']['size'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Last Backup:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['database']['last_backup'] }}</span>
                </div>
            </div>
        </div>

        <!-- Storage Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Storage</h3>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-{{ $status['storage']['status'] === 'healthy' ? 'green' : ($status['storage']['status'] === 'warning' ? 'yellow' : 'red') }}-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($status['storage']['status']) }}</span>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Space:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['storage']['total'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Used Space:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['storage']['used'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Available:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['storage']['available'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Usage:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['storage']['percentage'] }}%</span>
                </div>
            </div>
        </div>

        <!-- Application Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Application</h3>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Running</span>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Laravel Version:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['app']['laravel_version'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">PHP Version:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['app']['php_version'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Environment:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['app']['environment'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Debug Mode:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['app']['debug'] ? 'Enabled' : 'Disabled' }}</span>
                </div>
            </div>
        </div>

        <!-- Cache Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Cache</h3>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-{{ $status['cache']['status'] === 'working' ? 'green' : 'red' }}-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($status['cache']['status']) }}</span>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Driver:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['cache']['driver'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Config Cached:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['cache']['config_cached'] ? 'Yes' : 'No' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Routes Cached:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['cache']['routes_cached'] ? 'Yes' : 'No' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Views Cached:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['cache']['views_cached'] ? 'Yes' : 'No' }}</span>
                </div>
            </div>
        </div>

        <!-- Queue Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Queue</h3>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-{{ $status['queue']['status'] === 'running' ? 'green' : 'yellow' }}-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($status['queue']['status']) }}</span>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Driver:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['queue']['driver'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Pending Jobs:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['queue']['pending'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Failed Jobs:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['queue']['failed'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Workers:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['queue']['workers'] }}</span>
                </div>
            </div>
        </div>

        <!-- Security Status -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Security</h3>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-{{ $status['security']['score'] >= 80 ? 'green' : ($status['security']['score'] >= 60 ? 'yellow' : 'red') }}-500 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['security']['score'] }}%</span>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">HTTPS:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['security']['https'] ? 'Enabled' : 'Disabled' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">APP_KEY Set:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['security']['app_key'] ? 'Yes' : 'No' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Directory Permissions:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['security']['permissions'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Security Headers:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $status['security']['headers'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- System Logs -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent System Events</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Message</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                        @forelse($status['logs']['recent_logs'] as $log)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        {{ $log['level'] === 'error' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                                           ($log['level'] === 'warning' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                                        {{ ucfirst($log['level']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                    {{ $log['message'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $log['time'] }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No recent system events
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button onclick="clearCache()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Clear Cache
                </button>
                <button onclick="optimizeDatabase()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Optimize DB
                </button>
                <button onclick="generateBackup()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Create Backup
                </button>
                <button onclick="runMaintenance()" class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Maintenance Mode
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function clearCache() {
    if (confirm('Are you sure you want to clear the application cache?')) {
        fetch('/admin/system/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              alert(data.message);
              location.reload();
          });
    }
}

function optimizeDatabase() {
    if (confirm('Are you sure you want to optimize the database?')) {
        fetch('/admin/system/optimize-database', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              alert(data.message);
              location.reload();
          });
    }
}

function generateBackup() {
    if (confirm('Are you sure you want to create a system backup?')) {
        fetch('/admin/system/backup', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              alert(data.message);
          });
    }
}

function runMaintenance() {
    if (confirm('Are you sure you want to enable maintenance mode?')) {
        fetch('/admin/system/maintenance', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              alert(data.message);
          });
    }
}
</script>
@endsection
