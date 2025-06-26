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
                <button onclick="showConfirmModal('clearCache', 'Clear Application Cache', 'Are you sure you want to clear all application cache? This will remove cached config, routes, and views.', 'Clear Cache')"
                    class="group relative bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Clear Cache
                    </span>
                </button>
                <button onclick="showConfirmModal('optimizeDatabase', 'Optimize Database', 'Are you sure you want to optimize the database? This will clean up and reorganize database tables for better performance.', 'Optimize DB')"
                    class="group relative bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path>
                        </svg>
                        Optimize DB
                    </span>
                </button>
                <button onclick="showConfirmModal('generateBackup', 'Create System Backup', 'Are you sure you want to create a backup? This will generate a backup of your database and important files.', 'Create Backup')"
                    class="group relative bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        Create Backup
                    </span>
                </button>
                <button onclick="showConfirmModal('runMaintenance', 'Toggle Maintenance Mode', 'Are you sure you want to enable maintenance mode? This will make the site unavailable to regular users.', 'Enable Maintenance')"
                    class="group relative bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                    <span class="flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Maintenance Mode
                    </span>
                </button>
            </div>
        </div>
    </div>

   <!-- Custom Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm hidden z-50 transition-all duration-200">
    <div class="flex items-center justify-center min-h-screen px-3">
        <div id="modalContent" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-xs w-full transform transition-all duration-200 scale-95 opacity-0">
            <!-- Modal Header -->
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center">
                    <div id="modalIcon" class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center mr-2">
                        <!-- Icon will be dynamically inserted -->
                    </div>
                    <h3 id="modalTitle" class="text-base font-medium text-gray-900 dark:text-white">
                        Confirm Action
                    </h3>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="px-4 py-3">
                <p id="modalMessage" class="text-sm text-gray-600 dark:text-gray-300">
                    Are you sure you want to perform this action?
                </p>
            </div>

            <!-- Modal Footer -->
            <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 rounded-b-lg flex justify-end space-x-2">
                <button onclick="hideConfirmModal()"
                    class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-md hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-150">
                    Cancel
                </button>
                <button id="modalConfirmBtn" onclick="confirmAction()"
                    class="px-3 py-1.5 text-xs font-medium text-white rounded-md transition-all duration-150 hover:scale-[1.03]">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
</div>

<script>
let currentAction = null;

function showConfirmModal(action, title, message, buttonText) {
    currentAction = action;

    // Set modal content
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('modalConfirmBtn').textContent = buttonText;

    // Set icon and button color based on action
    const modalIcon = document.getElementById('modalIcon');
    const modalConfirmBtn = document.getElementById('modalConfirmBtn');

    switch(action) {
        case 'clearCache':
            modalIcon.innerHTML = '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';
            modalIcon.className = 'flex-shrink-0 w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3';
            modalConfirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-all duration-200 transform hover:scale-105';
            break;
        case 'optimizeDatabase':
            modalIcon.innerHTML = '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>';
            modalIcon.className = 'flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mr-3';
            modalConfirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md transition-all duration-200 transform hover:scale-105';
            break;
        case 'generateBackup':
            modalIcon.innerHTML = '<svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>';
            modalIcon.className = 'flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mr-3';
            modalConfirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 rounded-md transition-all duration-200 transform hover:scale-105';
            break;
        case 'runMaintenance':
            modalIcon.innerHTML = '<svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.864-.833-2.634 0L5.232 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>';
            modalIcon.className = 'flex-shrink-0 w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center mr-3';
            modalConfirmBtn.className = 'px-4 py-2 text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-md transition-all duration-200 transform hover:scale-105';
            break;
    }

    // Show modal with animation
    const modal = document.getElementById('confirmModal');
    const modalContent = document.getElementById('modalContent');

    modal.classList.remove('hidden');

    // Trigger animation after showing
    setTimeout(() => {
        modal.classList.add('bg-opacity-50');
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function hideConfirmModal() {
    const modal = document.getElementById('confirmModal');
    const modalContent = document.getElementById('modalContent');

    // Hide with animation
    modalContent.classList.remove('scale-100', 'opacity-100');
    modalContent.classList.add('scale-95', 'opacity-0');
    modal.classList.remove('bg-opacity-50');

    setTimeout(() => {
        modal.classList.add('hidden');
        currentAction = null;
    }, 300);
}

function confirmAction() {
    if (!currentAction) return;

    // Hide modal first
    hideConfirmModal();

    // Show loading toast
    showToast('Processing...', 'info');

    // Execute the action
    switch(currentAction) {
        case 'clearCache':
            clearCache();
            break;
        case 'optimizeDatabase':
            optimizeDatabase();
            break;
        case 'generateBackup':
            generateBackup();
            break;
        case 'runMaintenance':
            runMaintenance();
            break;
    }
}

function showToast(message, type = 'success') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full opacity-0 ${
        type === 'success' ? 'bg-green-600' :
        type === 'error' ? 'bg-red-600' :
        type === 'info' ? 'bg-blue-600' : 'bg-gray-600'
    }`;

    toast.innerHTML = `
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' ?
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>' :
                    type === 'error' ?
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                }
            </svg>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(toast);

    // Show toast
    setTimeout(() => {
        toast.classList.remove('translate-x-full', 'opacity-0');
        toast.classList.add('translate-x-0', 'opacity-100');
    }, 100);

    // Hide toast after 3 seconds
    setTimeout(() => {
        toast.classList.remove('translate-x-0', 'opacity-100');
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}

function clearCache() {
    fetch('/admin/system/clear-cache', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
      .then(data => {
          showToast(data.message, 'success');
          setTimeout(() => location.reload(), 1500);
      })
      .catch(error => {
          showToast('Failed to clear cache', 'error');
      });
}

function optimizeDatabase() {
    fetch('/admin/system/optimize-database', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
      .then(data => {
          showToast(data.message, 'success');
          setTimeout(() => location.reload(), 1500);
      })
      .catch(error => {
          showToast('Failed to optimize database', 'error');
      });
}

function generateBackup() {
    fetch('/admin/system/backup', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
      .then(data => {
          showToast(data.message, 'success');
      })
      .catch(error => {
          showToast('Failed to create backup', 'error');
      });
}

function runMaintenance() {
    fetch('/admin/system/maintenance', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
      .then(data => {
          showToast(data.message, 'success');
      })
      .catch(error => {
          showToast('Failed to toggle maintenance mode', 'error');
      });
}

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideConfirmModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && !document.getElementById('confirmModal').classList.contains('hidden')) {
        hideConfirmModal();
    }
});
</script>
@endsection
