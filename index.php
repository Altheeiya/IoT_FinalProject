<?php
// index.php (tidak mengubah desain, hanya menautkan assets yang dibuat)
?>
<!doctype html>
<html lang="id" class="light">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Greenhouse — Dashboard</title>

  <!-- Font: Quicksand -->
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>

  <!-- == FILE CSS LOKAL == -->
  <link rel="stylesheet" href="assets/css/style.css">

  <script>
    tailwind.config = { darkMode: 'class' };
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
      document.documentElement.classList.add('dark');
    } else {
      document.documentElement.classList.remove('dark');
    }
  </script>
</head>
<body class="bg-gradient-to-b from-green-50 via-white to-pink-50 dark:from-gray-900 dark:to-gray-900 dark:via-gray-900 min-h-screen text-gray-800 dark:text-gray-200">

  <!-- Toast Notification Placeholder -->
  <div id="toast" class="success hidden">Pesan</div>

  <div class="max-w-7xl mx-auto p-6">
    <header class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-4xl font-bold text-emerald-700 dark:text-emerald-500">🌿 Greenhouse</h1>
        <p class="text-sm text-emerald-500 dark:text-emerald-400 mt-1">Real-time monitoring & control</p>
      </div>

      <!-- Dark Mode Toggle -->
<!-- Tombol Toggle Dark Mode -->
      <button id="toggle-dark-mode" 
        class="toggle-dark p-2 rounded-md border" 
        aria-checked="false" 
        title="Toggle Dark Mode">
        🌙
      </button>

    </header>

    <!-- Top summary cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <!-- Suhu -->
      <div class="card bg-gradient-to-br from-orange-50 to-orange-100 dark:bg-none p-5">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-sm text-orange-600 dark:text-orange-400 card-header">Suhu Udara</div>
            <div id="card-temp" class="text-3xl font-bold text-orange-800 card-value">- °C</div>
            <div id="sub-temp" class="text-xs text-gray-500 card-sub">Terakhir: -</div>
          </div>
          <i data-lucide="thermometer" class="w-10 h-10 text-orange-400"></i>
        </div>
      </div>

      <!-- Kelembapan Udara -->
      <div class="card bg-gradient-to-br from-blue-50 to-blue-100 dark:bg-none p-5">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-sm text-blue-600 dark:text-blue-400 card-header">Kelembapan Udara</div>
            <div id="card-hum" class="text-3xl font-bold text-blue-800 card-value">- %</div>
            <div id="sub-hum" class="text-xs text-gray-500 card-sub">Terakhir: -</div>
          </div>
          <i data-lucide="droplets" class="w-10 h-10 text-blue-400"></i>
        </div>
      </div>

      <!-- Intensitas Cahaya -->
      <div class="card bg-gradient-to-br from-yellow-50 to-yellow-100 dark:bg-none p-5">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-sm text-yellow-600 dark:text-yellow-400 card-header">Intensitas Cahaya</div>
            <div id="card-ldr" class="text-3xl font-bold text-yellow-800 card-value">- lx</div>
            <div id="sub-ldr" class="text-xs text-gray-500 card-sub">Terakhir: -</div>
          </div>
          <i data-lucide="sun" class="w-10 h-10 text-yellow-400"></i>
        </div>
      </div>

      <!-- Kelembapan Tanah -->
      <div class="card bg-gradient-to-br from-emerald-50 to-emerald-100 dark:bg-none p-5">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-sm text-emerald-700 dark:text-emerald-400 card-header">Kelembapan Tanah</div>
            <div id="card-soil" class="text-3xl font-bold text-emerald-900 card-value">- %</div>
            <div id="sub-soil" class="text-xs text-gray-500 card-sub">Terakhir: -</div>
          </div>
          <i data-lucide="sprout" class="w-10 h-10 text-emerald-400"></i>
        </div>
      </div>
    </section>

    <!-- Charts area (3 charts) -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
      <div class="card bg-white p-4">
        <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-3">Suhu (°C) / 24 titik</h3>
        <canvas id="chartTemp" height="180"></canvas>
      </div>
      <div class="card bg-white p-4">
        <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-3">Kelembapan Tanah (%) / 24 titik</h3>
        <canvas id="chartSoil" height="180"></canvas>
      </div>
      <div class="card bg-white p-4">
        <h3 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-3">Intensitas Cahaya (lx) / 24 titik</h3>
        <canvas id="chartLight" height="180"></canvas>
      </div>
    </section>

    <!-- Controls + Logs -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Controls -->
      <div class="card bg-gradient-to-br from-white to-emerald-50 dark:bg-none p-5">
        <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">Kontrol Aktuator</h3>

        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center space-x-3">
            <i data-lucide="shower-head" class="w-9 h-9 text-pink-500"></i>
            <div>
              <div class="text-sm text-gray-500 dark:text-gray-400">Pompa</div>
              <div id="status-pump" class="text-xl font-semibold text-pink-600">OFF</div>
            </div>
          </div>
          <button id="btn-pump" class="toggle-btn" data-code="pump" aria-pressed="false">OFF</button>
        </div>

        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center space-x-3">
            <i data-lucide="fan" class="w-9 h-9 text-sky-500"></i>
            <div>
              <div class="text-sm text-gray-500 dark:text-gray-400">Kipas</div>
              <div id="status-fan" class="text-xl font-semibold text-sky-600">OFF</div>
            </div>
          </div>
          <button id="btn-fan" class="toggle-btn" data-code="fan" aria-pressed="false">OFF</button>
        </div>

        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <i data-lucide="lightbulb" class="w-9 h-9 text-yellow-500"></i>
            <div>
              <div class="text-sm text-gray-500 dark:text-gray-400">Lampu</div>
              <div id="status-light" class="text-xl font-semibold text-yellow-600">OFF</div>
            </div>
          </div>
          <button id="btn-light" class="toggle-btn" data-code="light" aria-pressed="false">OFF</button>
        </div>

      </div>

      <!-- Logs: larger column -->
      <div class="card col-span-2 bg-white p-5">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300">Log Aktivitas Terakhir</h3>
          <button id="btn-clear-logs" class="text-xs text-rose-500 hover:underline">Bersihkan Log</button>
        </div>

        <div id="logList" class="space-y-3 max-h-[300px] overflow-y-auto pr-2">
          <div class="text-gray-400">Memuat log...</div>
        </div>
      </div>
    </section>

    <footer class="mt-6 text-center text-xs text-gray-400 dark:text-gray-600">Dashboard v2.0 • Modernized Version</footer>
  </div>

  <!-- App JS -->
  <script src="assets/js/app.js" defer></script>
  <script>lucide.createIcons();</script>
</body>
</html>
