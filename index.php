<!doctype html>
<html lang="id" class="light">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Greenhouse — Dashboard</title>

    <!-- Font: Quicksand -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Flatpickr for date range -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">

    <script>
        tailwind.config = {
            darkMode: 'class'
        };
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>

<body
    class="bg-gradient-to-b from-green-50 via-white to-pink-50 dark:from-gray-900 dark:to-gray-800 dark:via-gray-900 min-h-screen text-gray-800 dark:text-gray-200 transition-colors duration-300">

    <!-- Toast Notification -->
    <div id="toast" class="success hidden">Pesan</div>

    <!-- Alert Container -->
    <div id="alert-container" class="fixed top-4 right-4 z-50 space-y-2 max-w-md"></div>

    <div class="max-w-[1600px] mx-auto p-4 lg:p-6">

        <!-- Header -->
        <header class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl lg:text-4xl font-bold text-emerald-700 dark:text-emerald-400">🌿 Greenhouse
                    Dashboard</h1>
                <p class="text-sm text-emerald-600 dark:text-emerald-500 mt-1">Real-time monitoring & intelligent
                    control</p>
            </div>

            <div class="flex items-center gap-3">
                <!-- Connection Status -->
                <div id="connection-status"
                    class="flex items-center gap-2 px-3 py-2 rounded-lg bg-white dark:bg-gray-800 shadow-sm">
                    <div class="w-2 h-2 rounded-full bg-gray-400" id="status-indicator"></div>
                    <span class="text-xs font-medium" id="status-text">Checking...</span>
                </div>

                <!-- Dark Mode Toggle -->
                <button id="toggle-dark-mode"
                    class="toggle-dark p-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all"
                    aria-checked="false" title="Toggle Dark Mode">
                    🌙
                </button>
            </div>
        </header>

        <!-- Dashboard Overview Section -->
        <section class="mb-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- System Status Card -->
                <div class="card bg-white dark:bg-gray-800 p-5 col-span-1 lg:col-span-2">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-700 dark:text-gray-200">📊 System Overview</h2>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Mode:</span>
                            <button id="mode-toggle" class="mode-toggle-btn" data-mode="manual">
                                <span class="mode-text">MANUAL</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="text-center p-3 rounded-lg bg-orange-50 dark:bg-orange-900/20">
                            <div class="text-2xl">🌡️</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Temperature</div>
                            <div id="overview-temp" class="text-sm font-bold text-orange-700 dark:text-orange-400">--
                            </div>
                        </div>
                        <div class="text-center p-3 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                            <div class="text-2xl">💧</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Humidity</div>
                            <div id="overview-hum" class="text-sm font-bold text-blue-700 dark:text-blue-400">--</div>
                        </div>
                        <div class="text-center p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20">
                            <div class="text-2xl">☀️</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Light</div>
                            <div id="overview-light" class="text-sm font-bold text-yellow-700 dark:text-yellow-400">--
                                lux
                            </div>
                        </div>
                        <div class="text-center p-3 rounded-lg bg-emerald-50 dark:bg-emerald-900/20">
                            <div class="text-2xl">🌱</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Soil</div>
                            <div id="overview-soil" class="text-sm font-bold text-emerald-700 dark:text-emerald-400">--
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-600 dark:text-gray-400">Actuators Active:</span>
                            <span id="actuators-active" class="font-bold text-emerald-600 dark:text-emerald-400">0 /
                                3</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card bg-white dark:bg-gray-800 p-5">
                    <h2 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">⚡ Quick Actions</h2>
                    <div class="space-y-2">
                        <button id="btn-export"
                            class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2">
                            <span>📥</span> Export Data
                        </button>
                        <button id="btn-threshold-settings"
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2">
                            <span>⚙️</span> Threshold Settings
                        </button>
                        <button id="btn-refresh-stats"
                            class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm font-medium transition-all flex items-center justify-center gap-2">
                            <span>🔄</span> Refresh Stats
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Statistics Section -->
        <section class="mb-6">
            <div class="card bg-white dark:bg-gray-800 p-5">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                    <h2 class="text-lg font-bold text-gray-700 dark:text-gray-200">📈 Statistics</h2>
                    <div class="flex items-center gap-2">
                        <select id="stats-period"
                            class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm">
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="stat-box">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Temperature (°C)</div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">Min</div>
                                <div id="stat-temp-min" class="text-lg font-bold text-blue-600 dark:text-blue-400">--
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">Avg</div>
                                <div id="stat-temp-avg" class="text-lg font-bold text-green-600 dark:text-green-400">--
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">Max</div>
                                <div id="stat-temp-max" class="text-lg font-bold text-red-600 dark:text-red-400">--
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-box">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Humidity (%)</div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">Min</div>
                                <div id="stat-hum-min" class="text-lg font-bold text-blue-600 dark:text-blue-400">--
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">Avg</div>
                                <div id="stat-hum-avg" class="text-lg font-bold text-green-600 dark:text-green-400">--
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">Max</div>
                                <div id="stat-hum-max" class="text-lg font-bold text-red-600 dark:text-red-400">--</div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-box">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Light (lx)</div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">Min</div>
                                <div id="stat-light-min" class="text-lg font-bold text-blue-600 dark:text-blue-400">--
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">Avg</div>
                                <div id="stat-light-avg" class="text-lg font-bold text-green-600 dark:text-green-400">--
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">Max</div>
                                <div id="stat-light-max" class="text-lg font-bold text-red-600 dark:text-red-400">--
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="stat-box">
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">Soil Moisture (%)</div>
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">Min</div>
                                <div id="stat-soil-min" class="text-lg font-bold text-blue-600 dark:text-blue-400">--
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">Avg</div>
                                <div id="stat-soil-avg" class="text-lg font-bold text-green-600 dark:text-green-400">--
                                </div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 dark:text-gray-500">Max</div>
                                <div id="stat-soil-max" class="text-lg font-bold text-red-600 dark:text-red-400">--
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Charts Section with Time Filter -->
        <section class="mb-6">
            <div class="card bg-white dark:bg-gray-800 p-5">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                    <h2 class="text-lg font-bold text-gray-700 dark:text-gray-200">📉 Historical Data</h2>
                    <div class="flex items-center gap-2">
                        <input type="text" id="date-range" placeholder="Select date range"
                            class="px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm w-64">
                        <button id="btn-reset-filter"
                            class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm">Reset</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="chart-container">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2">🌡️ Suhu (°C)</h3>
                        <canvas id="chartTemp" height="200"></canvas>
                    </div>
                    <div class="chart-container">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2">💧 Kelembapan Udara (%)
                        </h3>
                        <canvas id="chartHumidity" height="200"></canvas>
                    </div>
                    <div class="chart-container">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2">☀️ Intensitas Cahaya
                            (lx)</h3>
                        <canvas id="chartLight" height="200"></canvas>
                    </div>
                    <div class="chart-container">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-300 mb-2">🌱 Kelembapan Tanah (%)
                        </h3>
                        <canvas id="chartSoil" height="200"></canvas>
                    </div>
                </div>
            </div>
        </section>

        <!-- Controls + Logs -->
        <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Controls -->
            <div class="card bg-white dark:bg-gray-800 p-5">
                <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200 mb-4">🎛️ Kontrol Aktuator</h3>

                <div class="flex items-center justify-between mb-4 p-3 rounded-lg bg-pink-50 dark:bg-pink-900/20">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="shower-head" class="w-8 h-8 text-pink-500"></i>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Pompa</div>
                            <div id="status-pump" class="text-lg font-semibold text-pink-600 dark:text-pink-400">OFF
                            </div>
                        </div>
                    </div>
                    <button id="btn-pump" class="toggle-btn" data-code="pump" aria-pressed="false">OFF</button>
                </div>

                <div class="flex items-center justify-between mb-4 p-3 rounded-lg bg-sky-50 dark:bg-sky-900/20">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="fan" class="w-8 h-8 text-sky-500"></i>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Kipas</div>
                            <div id="status-fan" class="text-lg font-semibold text-sky-600 dark:text-sky-400">OFF</div>
                        </div>
                    </div>
                    <button id="btn-fan" class="toggle-btn" data-code="fan" aria-pressed="false">OFF</button>
                </div>

                <div class="flex items-center justify-between p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20">
                    <div class="flex items-center space-x-3">
                        <i data-lucide="lightbulb" class="w-8 h-8 text-yellow-500"></i>
                        <div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Lampu</div>
                            <div id="status-light" class="text-lg font-semibold text-yellow-600 dark:text-yellow-400">
                                OFF</div>
                        </div>
                    </div>
                    <button id="btn-light" class="toggle-btn" data-code="light" aria-pressed="false">OFF</button>
                </div>
            </div>

            <!-- Logs -->
            <div class="card col-span-1 lg:col-span-2 bg-white dark:bg-gray-800 p-5">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200">📜 Log Aktivitas Terakhir</h3>
                    <button id="btn-clear-logs" class="text-xs text-rose-500 hover:underline">Bersihkan Log</button>
                </div>

                <div id="logList" class="space-y-2 max-h-[300px] overflow-y-auto pr-2">
                    <div class="text-gray-400">Memuat log...</div>
                </div>
            </div>
        </section>

        <footer class="mt-6 text-center text-xs text-gray-500 dark:text-gray-600">
            Dashboard v3.0 • Enhanced IoT Greenhouse System
        </footer>
    </div>

    <!-- Threshold Settings Modal -->
    <div id="threshold-modal" class="modal hidden">
        <div class="modal-content max-w-2xl">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">⚙️ Threshold Settings</h2>
                <button
                    class="close-modal text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 text-2xl">&times;</button>
            </div>

            <div class="space-y-4">
                <div class="threshold-item">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">🌡️ Temperature
                        (°C)</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Min</label>
                            <input type="number" id="thresh-temp-min" step="0.1" class="threshold-input">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Max</label>
                            <input type="number" id="thresh-temp-max" step="0.1" class="threshold-input">
                        </div>
                    </div>
                </div>

                <div class="threshold-item">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">💧 Humidity
                        (%)</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Min</label>
                            <input type="number" id="thresh-hum-min" step="0.1" class="threshold-input">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Max</label>
                            <input type="number" id="thresh-hum-max" step="0.1" class="threshold-input">
                        </div>
                    </div>
                </div>

                <div class="threshold-item">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">☀️ Light
                        (lx)</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Min</label>
                            <input type="number" id="thresh-light-min" class="threshold-input">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Max</label>
                            <input type="number" id="thresh-light-max" class="threshold-input">
                        </div>
                    </div>
                </div>

                <div class="threshold-item">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">🌱 Soil Moisture
                        (%)</label>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Min</label>
                            <input type="number" id="thresh-soil-min" step="0.1" class="threshold-input">
                        </div>
                        <div>
                            <label class="text-xs text-gray-600 dark:text-gray-400">Max</label>
                            <input type="number" id="thresh-soil-max" step="0.1" class="threshold-input">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    class="close-modal px-4 py-2 bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500 rounded-lg text-sm font-medium">Cancel</button>
                <button id="save-thresholds"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium">Save
                    Settings</button>
            </div>
        </div>
    </div>

    <!-- App JS -->
    <script src="assets/js/app.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>