// Enhanced Greenhouse Dashboard JavaScript
// Version 3.0 - Full Featured

// ============ UTILITIES ============
function $id(id) {
  return document.getElementById(id);
}

function showToast(msg, type = "success") {
  const t = $id("toast");
  t.textContent = msg;
  t.className = `${type} show`;
  setTimeout(() => (t.className = type), 3000);
}

function showAlert(message, type = "warning") {
  const container = $id("alert-container");
  const alert = document.createElement("div");
  alert.className = `alert-notification ${type}`;
  alert.innerHTML = `
    <div class="flex items-start gap-3">
      <div class="text-2xl">${type === "danger" ? "🚨" : "⚠️"}</div>
      <div class="flex-1">
        <div class="font-semibold text-sm">${type === "danger" ? "Critical Alert" : "Warning"}</div>
        <div class="text-xs mt-1">${message}</div>
      </div>
      <button onclick="this.parentElement.parentElement.remove()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">&times;</button>
    </div>
  `;
  container.appendChild(alert);
  setTimeout(() => alert.remove(), 10000);
}

// ============ STATE MANAGEMENT ============
const appState = {
  mode: 'manual', // manual or auto
  thresholds: {},
  charts: {},
  dateFilter: null,
  stats: {},
  connectionStatus: 'checking'
};

// ============ DARK MODE ============
function initDarkMode() {
  const darkToggle = $id("toggle-dark-mode");
  if (!darkToggle) return;

  const html = document.documentElement;
  const setState = (isDark) => {
    darkToggle.setAttribute("aria-checked", isDark ? "true" : "false");
    if (isDark) {
      html.classList.add("dark");
      darkToggle.innerHTML = "☀️";
    } else {
      html.classList.remove("dark");
      darkToggle.innerHTML = "🌙";
    }
    localStorage.theme = isDark ? "dark" : "light";
    
    // Update chart themes
    updateChartThemes(isDark);
  };

  // Initial state
  const isDark = localStorage.theme === "dark" || 
    (!("theme" in localStorage) && window.matchMedia("(prefers-color-scheme: dark)").matches);
  setState(isDark);

  darkToggle.addEventListener("click", () => {
    const currentlyDark = darkToggle.getAttribute("aria-checked") === "true";
    setState(!currentlyDark);
  });
}

// ============ CHARTS ============
function initCharts() {
  const isDark = document.documentElement.classList.contains('dark');
  const gridColor = isDark ? '#374151' : '#e5e7eb';
  const textColor = isDark ? '#9ca3af' : '#6b7280';

  const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { 
        display: true,
        labels: { color: textColor }
      },
      tooltip: {
        backgroundColor: isDark ? '#1f2937' : '#ffffff',
        titleColor: isDark ? '#e5e7eb' : '#111827',
        bodyColor: isDark ? '#e5e7eb' : '#111827',
        borderColor: isDark ? '#374151' : '#e5e7eb',
        borderWidth: 1
      }
    },
    scales: {
      x: {
        grid: { color: gridColor },
        ticks: { color: textColor }
      },
      y: {
        grid: { color: gridColor },
        ticks: { color: textColor }
      }
    }
  };

  // Temperature Chart
  const ctxTemp = $id("chartTemp").getContext("2d");
  appState.charts.temp = new Chart(ctxTemp, {
    type: "line",
    data: {
      labels: [],
      datasets: [{
        label: "Suhu (°C)",
        data: [],
        tension: 0.4,
        borderColor: "#f97316",
        backgroundColor: "rgba(249,115,22,0.1)",
        pointRadius: 3,
        pointHoverRadius: 5,
        fill: true
      }]
    },
    options: { ...commonOptions, scales: { ...commonOptions.scales, y: { ...commonOptions.scales.y, beginAtZero: false } } }
  });

  // Humidity Chart
  const ctxHum = $id("chartHumidity").getContext("2d");
  appState.charts.humidity = new Chart(ctxHum, {
    type: "line",
    data: {
      labels: [],
      datasets: [{
        label: "Kelembapan (%)",
        data: [],
        tension: 0.4,
        borderColor: "#3b82f6",
        backgroundColor: "rgba(59,130,246,0.1)",
        pointRadius: 3,
        pointHoverRadius: 5,
        fill: true
      }]
    },
    options: commonOptions
  });

  // Light Chart
  const ctxLight = $id("chartLight").getContext("2d");
  appState.charts.light = new Chart(ctxLight, {
    type: "line",
    data: {
      labels: [],
      datasets: [{
        label: "Cahaya (lux)",
        data: [],
        tension: 0.4,
        borderColor: "#facc15",
        backgroundColor: "rgba(250,204,21,0.1)",
        pointRadius: 3,
        pointHoverRadius: 5,
        fill: true
      }]
    },
    options: commonOptions
  });

  // Soil Chart
  const ctxSoil = $id("chartSoil").getContext("2d");
  appState.charts.soil = new Chart(ctxSoil, {
    type: "line",
    data: {
      labels: [],
      datasets: [{
        label: "Kelembapan Tanah (%)",
        data: [],
        tension: 0.4,
        borderColor: "#10b981",
        backgroundColor: "rgba(16,185,129,0.1)",
        pointRadius: 3,
        pointHoverRadius: 5,
        fill: true
      }]
    },
    options: commonOptions
  });
}

function updateChartThemes(isDark) {
  const gridColor = isDark ? '#374151' : '#e5e7eb';
  const textColor = isDark ? '#9ca3af' : '#6b7280';

  Object.values(appState.charts).forEach(chart => {
    if (chart && chart.options) {
      chart.options.plugins.legend.labels.color = textColor;
      chart.options.plugins.tooltip.backgroundColor = isDark ? '#1f2937' : '#ffffff';
      chart.options.plugins.tooltip.titleColor = isDark ? '#e5e7eb' : '#111827';
      chart.options.plugins.tooltip.bodyColor = isDark ? '#e5e7eb' : '#111827';
      chart.options.plugins.tooltip.borderColor = isDark ? '#374151' : '#e5e7eb';
      chart.options.scales.x.grid.color = gridColor;
      chart.options.scales.x.ticks.color = textColor;
      chart.options.scales.y.grid.color = gridColor;
      chart.options.scales.y.ticks.color = textColor;
      chart.update();
    }
  });
}

// ============ DATA FETCHING ============
async function fetchAndUpdate() {
  try {
    const params = new URLSearchParams();
    if (appState.dateFilter) {
      params.append('start_date', appState.dateFilter.start);
      params.append('end_date', appState.dateFilter.end);
    }
    
    const url = `api/get_data.php?${params.toString()}`;
    const res = await fetch(url, { cache: "no-store" });
    const data = await res.json();
    
    if (!data.success) {
      console.error("API error", data);
      return;
    }

    updateSensorCards(data.latest || {});
    updateOverview(data.latest || {});
    updateActuators(data.actuators || {});
    updateLogs(data.logs || []);
    updateCharts(data.history || {});
    checkAlerts(data.latest || {});
    updateConnectionStatus(data.devices || {});

  } catch (err) {
    console.error("fetchAndUpdate error", err);
    updateConnectionStatus({ ESP1: { is_online: 0 }, ESP2: { is_online: 0 } });
  }
}

function updateSensorCards(latest) {
  // Temperature
  const temp = latest.temperature !== null ? parseFloat(latest.temperature).toFixed(1) : "--";
  $id("overview-temp").textContent = temp + "°C";
  
  // Humidity
  const hum = latest.humidity !== null ? parseFloat(latest.humidity).toFixed(1) : "--";
  $id("overview-hum").textContent = hum + "%";
  
  // Light
  const light = latest.light !== null ? parseInt(latest.light) : "--";
  $id("overview-light").textContent = light + " lux";
  
  // Soil
  const soil = latest.soil !== null ? parseFloat(latest.soil).toFixed(1) : "--";
  $id("overview-soil").textContent = soil + "%";
}

function updateOverview(latest) {
  // Already updated in updateSensorCards
}

function updateActuators(actuators) {
  let activeCount = 0;
  
  ["pump", "fan", "light"].forEach(code => {
    const info = actuators[code] || null;
    const btn = document.querySelector(`[data-code="${code}"]`);
    const statusEl = $id("status-" + code);
    
    if (info) {
      const isOn = info.status == 1 || info.status === "1";
      if (isOn) activeCount++;
      
      if (btn) {
        btn.setAttribute("aria-pressed", isOn ? "true" : "false");
        btn.textContent = isOn ? "ON" : "OFF";
      }
      if (statusEl) statusEl.textContent = isOn ? "ON" : "OFF";
    }
  });
  
  $id("actuators-active").textContent = `${activeCount} / 3`;
}

function updateLogs(logs) {
  const logList = $id("logList");
  logList.innerHTML = "";
  
  if (logs && logs.length) {
    logs.forEach(log => {
      const item = document.createElement("div");
      item.className = "p-3 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 hover:shadow-sm transition";
      item.innerHTML = `
        <div class="text-sm text-gray-700 dark:text-gray-300 font-medium">${log.detail || ""}</div>
        <div class="text-xs text-gray-500 dark:text-gray-500 mt-1">${log.created_at || ""}</div>
      `;
      logList.appendChild(item);
    });
  } else {
    logList.innerHTML = '<div class="text-gray-400 text-center py-4">Tidak ada log.</div>';
  }
}

function updateCharts(history) {
  // Temperature
  if (history.temp && appState.charts.temp) {
    appState.charts.temp.data.labels = history.temp.map(p => 
      new Date(p.ts).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
    );
    appState.charts.temp.data.datasets[0].data = history.temp.map(p => parseFloat(p.value));
    appState.charts.temp.update();
  }

  // Humidity
  if (history.humidity && appState.charts.humidity) {
    appState.charts.humidity.data.labels = history.humidity.map(p => 
      new Date(p.ts).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
    );
    appState.charts.humidity.data.datasets[0].data = history.humidity.map(p => parseFloat(p.value));
    appState.charts.humidity.update();
  }

  // Light
  if (history.light && appState.charts.light) {
    appState.charts.light.data.labels = history.light.map(p => 
      new Date(p.ts).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
    );
    appState.charts.light.data.datasets[0].data = history.light.map(p => parseInt(p.value));
    appState.charts.light.update();
  }

  // Soil
  if (history.soil && appState.charts.soil) {
    appState.charts.soil.data.labels = history.soil.map(p => 
      new Date(p.ts).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
    );
    appState.charts.soil.data.datasets[0].data = history.soil.map(p => parseFloat(p.value));
    appState.charts.soil.update();
  }
}

// ============ ALERTS ============
function checkAlerts(latest) {
  if (!appState.thresholds || Object.keys(appState.thresholds).length === 0) return;

  const sensors = {
    temperature: { value: latest.temperature, label: "Suhu", unit: "°C" },
    humidity: { value: latest.humidity, label: "Kelembapan", unit: "%" },
    light: { value: latest.light, label: "Cahaya", unit: "lux" },
    soil: { value: latest.soil, label: "Kelembapan Tanah", unit: "%" }
  };

  Object.keys(sensors).forEach(key => {
    const sensor = sensors[key];
    const thresh = appState.thresholds[key];
    
    if (sensor.value === null || !thresh) return;

    if (thresh.min_value !== null && sensor.value < parseFloat(thresh.min_value)) {
      showAlert(`${sensor.label} terlalu rendah: ${sensor.value}${sensor.unit}`, "danger");
    } else if (thresh.max_value !== null && sensor.value > parseFloat(thresh.max_value)) {
      showAlert(`${sensor.label} terlalu tinggi: ${sensor.value}${sensor.unit}`, "danger");
    }
  });
}

// ============ CONNECTION STATUS ============
function updateConnectionStatus(devices) {
  const esp1 = devices.ESP1 || { is_online: 0 };
  const esp2 = devices.ESP2 || { is_online: 0 };
  
  const indicator = $id("status-indicator");
  const statusText = $id("status-text");
  
  const allOnline = esp1.is_online && esp2.is_online;
  const someOnline = esp1.is_online || esp2.is_online;
  
  if (allOnline) {
    indicator.className = "w-2 h-2 rounded-full bg-green-500 online";
    statusText.textContent = "All Online";
    statusText.className = "text-xs font-medium text-green-600 dark:text-green-400";
  } else if (someOnline) {
    indicator.className = "w-2 h-2 rounded-full bg-yellow-500";
    statusText.textContent = "Partial";
    statusText.className = "text-xs font-medium text-yellow-600 dark:text-yellow-400";
  } else {
    indicator.className = "w-2 h-2 rounded-full bg-red-500 offline";
    statusText.textContent = "Offline";
    statusText.className = "text-xs font-medium text-red-600 dark:text-red-400";
  }
}

// ============ ACTUATOR CONTROL ============
async function sendActuator(code, newState) {
  try {
    const form = new FormData();
    form.append("code", code);
    form.append("status", newState ? 1 : 0);
    form.append("mode", appState.mode);
    
    const res = await fetch("api/update_actuator.php", {
      method: "POST",
      body: form
    });
    const data = await res.json();
    
    if (data.success) {
      showToast("Perintah berhasil dikirim", "success");
      await fetchAndUpdate();
    } else {
      showToast("Gagal: " + (data.msg || "unknown"), "error");
    }
  } catch (err) {
    console.error(err);
    showToast("Koneksi gagal", "error");
  }
}

// ============ MODE TOGGLE ============
async function toggleMode() {
  const newMode = appState.mode === 'manual' ? 'auto' : 'manual';
  
  try {
    const form = new FormData();
    form.append("mode", newMode);
    
    const res = await fetch("api/set_mode.php", {
      method: "POST",
      body: form
    });
    const data = await res.json();
    
    if (data.success) {
      appState.mode = newMode;
      const btn = $id("mode-toggle");
      btn.dataset.mode = newMode;
      btn.querySelector('.mode-text').textContent = newMode.toUpperCase();
      showToast(`Mode ${newMode.toUpperCase()} aktif`, "info");
    } else {
      showToast("Gagal mengubah mode", "error");
    }
  } catch (err) {
    console.error(err);
    showToast("Koneksi gagal", "error");
  }
}

// ============ THRESHOLDS ============
async function loadThresholds() {
  try {
    const res = await fetch("api/get_thresholds.php");
    const data = await res.json();
    
    if (data.success && data.thresholds) {
      appState.thresholds = data.thresholds;
      populateThresholdForm();
    }
  } catch (err) {
    console.error("Error loading thresholds:", err);
  }
}

function populateThresholdForm() {
  const types = ['temperature', 'humidity', 'light', 'soil'];
  types.forEach(type => {
    const thresh = appState.thresholds[type];
    if (thresh) {
      const minInput = $id(`thresh-${type === 'temperature' ? 'temp' : type === 'humidity' ? 'hum' : type}-min`);
      const maxInput = $id(`thresh-${type === 'temperature' ? 'temp' : type === 'humidity' ? 'hum' : type}-max`);
      
      if (minInput) minInput.value = thresh.min_value || '';
      if (maxInput) maxInput.value = thresh.max_value || '';
    }
  });
}

async function saveThresholds() {
  const thresholds = {
    temperature: {
      min: $id('thresh-temp-min').value,
      max: $id('thresh-temp-max').value
    },
    humidity: {
      min: $id('thresh-hum-min').value,
      max: $id('thresh-hum-max').value
    },
    light: {
      min: $id('thresh-light-min').value,
      max: $id('thresh-light-max').value
    },
    soil: {
      min: $id('thresh-soil-min').value,
      max: $id('thresh-soil-max').value
    }
  };

  try {
    const res = await fetch("api/save_thresholds.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ thresholds })
    });
    const data = await res.json();
    
    if (data.success) {
      showToast("Threshold berhasil disimpan", "success");
      closeModal();
      await loadThresholds();
    } else {
      showToast("Gagal menyimpan threshold", "error");
    }
  } catch (err) {
    console.error(err);
    showToast("Koneksi gagal", "error");
  }
}

// ============ STATISTICS ============
async function loadStatistics(period = 'today') {
  try {
    const res = await fetch(`api/get_statistics.php?period=${period}`);
    const data = await res.json();
    
    if (data.success && data.stats) {
      displayStatistics(data.stats);
    }
  } catch (err) {
    console.error("Error loading statistics:", err);
  }
}

function displayStatistics(stats) {
  const types = ['temp', 'hum', 'light', 'soil'];
  types.forEach(type => {
    const stat = stats[type === 'temp' ? 'temperature' : type === 'hum' ? 'humidity' : type];
    if (stat) {
      const minEl = $id(`stat-${type}-min`);
      const avgEl = $id(`stat-${type}-avg`);
      const maxEl = $id(`stat-${type}-max`);
      
      if (minEl) minEl.textContent = stat.min !== null ? parseFloat(stat.min).toFixed(1) : '--';
      if (avgEl) avgEl.textContent = stat.avg !== null ? parseFloat(stat.avg).toFixed(1) : '--';
      if (maxEl) maxEl.textContent = stat.max !== null ? parseFloat(stat.max).toFixed(1) : '--';
    }
  });
}

// ============ EXPORT DATA ============
async function exportData() {
  try {
    showToast("Mengunduh data...", "info");
    
    const params = new URLSearchParams();
    if (appState.dateFilter) {
      params.append('start_date', appState.dateFilter.start);
      params.append('end_date', appState.dateFilter.end);
    }
    
    window.location.href = `api/export_data.php?${params.toString()}`;
    
    setTimeout(() => {
      showToast("Download dimulai", "success");
    }, 1000);
  } catch (err) {
    console.error(err);
    showToast("Gagal mengekspor data", "error");
  }
}

// ============ DATE FILTER ============
function initDateFilter() {
  const dateInput = $id("date-range");
  if (!dateInput) return;

  flatpickr(dateInput, {
    mode: "range",
    dateFormat: "Y-m-d",
    maxDate: "today",
    onChange: function(selectedDates) {
      if (selectedDates.length === 2) {
        appState.dateFilter = {
          start: selectedDates[0].toISOString().split('T')[0],
          end: selectedDates[1].toISOString().split('T')[0]
        };
        fetchAndUpdate();
      }
    }
  });

  $id("btn-reset-filter")?.addEventListener("click", () => {
    appState.dateFilter = null;
    dateInput.value = "";
    fetchAndUpdate();
  });
}

// ============ MODAL CONTROLS ============
function openModal() {
  const modal = $id("threshold-modal");
  modal.classList.remove('hidden');
  setTimeout(() => modal.classList.add('show'), 10);
}

function closeModal() {
  const modal = $id("threshold-modal");
  modal.classList.remove('show');
  setTimeout(() => modal.classList.add('hidden'), 300);
}

// ============ CLEAR LOGS ============
async function clearLogs() {
  if (!confirm("Yakin ingin menghapus semua log?")) return;
  
  try {
    const res = await fetch("api/clear_logs.php", { method: "POST" });
    const data = await res.json();
    
    if (data.success) {
      showToast("Log berhasil dihapus", "success");
      fetchAndUpdate();
    } else {
      showToast("Gagal menghapus log", "error");
    }
  } catch (err) {
    console.error(err);
    showToast("Koneksi gagal", "error");
  }
}

// ============ BUTTON LISTENERS ============
function initButtons() {
  // Actuator buttons
  document.querySelectorAll(".toggle-btn").forEach(btn => {
    btn.addEventListener("click", async () => {
      if (appState.mode === 'auto') {
        showToast("Matikan mode AUTO untuk kontrol manual", "warning");
        return;
      }
      
      const code = btn.dataset.code;
      const isPressed = btn.getAttribute("aria-pressed") === "true";
      await sendActuator(code, !isPressed);
    });
  });

  // Mode toggle
  $id("mode-toggle")?.addEventListener("click", toggleMode);

  // Export button
  $id("btn-export")?.addEventListener("click", exportData);

  // Threshold settings button
  $id("btn-threshold-settings")?.addEventListener("click", () => {
    openModal();
    loadThresholds();
  });

  // Refresh stats button
  $id("btn-refresh-stats")?.addEventListener("click", () => {
    const period = $id("stats-period").value;
    loadStatistics(period);
  });

  // Clear logs button
  $id("btn-clear-logs")?.addEventListener("click", clearLogs);

  // Save thresholds button
  $id("save-thresholds")?.addEventListener("click", saveThresholds);

  // Close modal buttons
  document.querySelectorAll(".close-modal").forEach(btn => {
    btn.addEventListener("click", closeModal);
  });

  // Stats period selector
  $id("stats-period")?.addEventListener("change", (e) => {
    loadStatistics(e.target.value);
  });

  // Click outside modal to close
  $id("threshold-modal")?.addEventListener("click", (e) => {
    if (e.target.id === "threshold-modal") {
      closeModal();
    }
  });
}

// ============ INITIALIZATION ============
async function init() {
  console.log("🌿 Greenhouse Dashboard v3.0 initializing...");
  
  // Initialize Lucide icons
  if (window.lucide) window.lucide.createIcons();
  
  // Initialize components
  initDarkMode();
  initCharts();
  initButtons();
  initDateFilter();
  
  // Load initial data
  await loadThresholds();
  await loadStatistics('today');
  await fetchAndUpdate();
  
  // Start polling
  setInterval(fetchAndUpdate, 5000);
  
  console.log("✅ Dashboard ready!");
}

// Start application when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}
