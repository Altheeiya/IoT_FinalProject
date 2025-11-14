// assets/js/app.js

// -- helpers
function $id(id) {
  return document.getElementById(id);
}
function showToast(msg, type = "success") {
  const t = $id("toast");
  t.textContent = msg;
  t.className = type === "error" ? "error show" : "success show";
  setTimeout(
    () => (t.className = type === "error" ? "error" : "success"),
    3000
  );
}

// Init lucide icons (will be called in index)
if (window.lucide) window.lucide.createIcons();

// Dark mode toggle
const darkToggle = $id("toggle-dark-mode");
if (darkToggle) {
  const html = document.documentElement;
  const setState = (isDark) => {
    darkToggle.setAttribute("aria-checked", isDark ? "true" : "false");
    darkToggle.classList.toggle("active", isDark);
    if (isDark) html.classList.add("dark");
    else html.classList.remove("dark");
    localStorage.theme = isDark ? "dark" : "light";
  };
  // initial state
  setState(
    localStorage.theme === "dark" ||
      (!("theme" in localStorage) &&
        window.matchMedia("(prefers-color-scheme: dark)").matches)
  );
  darkToggle.addEventListener("click", () => {
    const isDark = darkToggle.getAttribute("aria-checked") !== "true";
    setState(isDark);
    darkToggle.innerHTML = isDark ? "☀️" : "🌙";
  });

}

// === Charts ===
let chartTemp = null,
  chartSoil = null,
  chartLight = null;
function initCharts() {
  const ctxT = document.getElementById("chartTemp").getContext("2d");
  chartTemp = new Chart(ctxT, {
    type: "line",
    data: {
      labels: [],
      datasets: [
        {
          label: "Suhu (°C)",
          data: [],
          tension: 0.3,
          borderColor: "#f97316",
          backgroundColor: "rgba(249,115,22,0.06)",
          pointRadius: 2,
        },
      ],
    },
    options: {
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: false } },
    },
  });

  const ctxS = document.getElementById("chartSoil").getContext("2d");
  chartSoil = new Chart(ctxS, {
    type: "line",
    data: {
      labels: [],
      datasets: [
        {
          label: "Soil (%)",
          data: [],
          tension: 0.3,
          borderColor: "#10b981",
          backgroundColor: "rgba(16,185,129,0.06)",
          pointRadius: 2,
        },
      ],
    },
    options: {
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } },
    },
  });

  const ctxL = document.getElementById("chartLight").getContext("2d");
  chartLight = new Chart(ctxL, {
    type: "line",
    data: {
      labels: [],
      datasets: [
        {
          label: "Light (lx)",
          data: [],
          tension: 0.3,
          borderColor: "#facc15",
          backgroundColor: "rgba(250,204,21,0.06)",
          pointRadius: 2,
        },
      ],
    },
    options: {
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } },
    },
  });
}

// === Update UI from API ===
async function fetchAndUpdate() {
  try {
    const res = await fetch("api/get_data.php", { cache: "no-store" });
    const j = await res.json();
    if (!j.success) {
      console.error("API error", j);
      return;
    }

    const latest = j.latest || {};
    // Cards
    $id("card-temp").textContent =
      (latest.temperature !== null ? latest.temperature : "-") + " °C";
    $id("card-hum").textContent =
      (latest.humidity !== null ? latest.humidity : "-") + " %";
    $id("card-ldr").textContent =
      (latest.light !== null ? latest.light : "-") + " lx";
    $id("card-soil").textContent =
      (latest.soil !== null ? latest.soil : "-") + " %";

    const timeStr = latest.ts ? new Date(latest.ts).toLocaleString() : "-";
    ["sub-temp", "sub-hum", "sub-ldr", "sub-soil"].forEach(
      (id) => ($id(id).textContent = "Terakhir: " + timeStr)
    );

    // Actuators
    const acts = j.actuators || {};
    ["pump", "fan", "light"].forEach((code) => {
      const info = acts[code] || null;
      const btn = document.querySelector(`[data-code="${code}"]`);
      const statusEl = $id("status-" + code);
      if (info) {
        const isOn = info.status == 1 || info.status === "1";
        if (btn) {
          btn.setAttribute("aria-pressed", isOn ? "true" : "false");
          btn.textContent = isOn ? "ON" : "OFF";
        }
        if (statusEl) statusEl.textContent = isOn ? "ON" : "OFF";
      }
    });

    // Logs
    const logList = $id("logList");
    logList.innerHTML = "";
    if (j.logs && j.logs.length) {
      j.logs.forEach((l) => {
        const item = document.createElement("div");
        item.className =
          "p-3 rounded-lg border flex items-start space-x-3 hover:shadow-sm transition";
        item.innerHTML = `<div class="text-sm text-gray-600 flex-1"><div class="font-semibold">${
          l.detail || ""
        }</div><div class="text-xs text-gray-400">${
          l.created_at || ""
        }</div></div>`;
        logList.appendChild(item);
      });
    } else {
      logList.innerHTML = '<div class="text-gray-400">Tidak ada log.</div>';
    }

    // Charts history
    const h = j.history || {};
    // temp
    if (h.temp) {
      chartTemp.data.labels = h.temp.map((p) =>
        new Date(p.ts).toLocaleTimeString()
      );
      chartTemp.data.datasets[0].data = h.temp.map((p) => p.value);
      chartTemp.update();
    }
    if (h.soil) {
      chartSoil.data.labels = h.soil.map((p) =>
        new Date(p.ts).toLocaleTimeString()
      );
      chartSoil.data.datasets[0].data = h.soil.map((p) => p.value);
      chartSoil.update();
    }
    if (h.light) {
      chartLight.data.labels = h.light.map((p) =>
        new Date(p.ts).toLocaleTimeString()
      );
      chartLight.data.datasets[0].data = h.light.map((p) => p.value);
      chartLight.update();
    }
  } catch (err) {
    console.error("fetchAndUpdate error", err);
  }
}

// === Toggle actuator from UI ===
async function sendActuator(code, newState) {
  try {
    const form = new FormData();
    form.append("code", code);
    form.append("status", newState ? 1 : 0);
    const res = await fetch("api/update_actuator.php", {
      method: "POST",
      body: form,
    });
    const j = await res.json();
    if (j.success) {
      showToast("Perintah dikirim");
      await fetchAndUpdate();
    } else {
      showToast("Gagal: " + (j.msg || "unknown"), "error");
    }
  } catch (err) {
    console.error(err);
    showToast("Koneksi gagal", "error");
  }
}

// attach button listeners
function initButtons() {
  document.querySelectorAll(".toggle-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const code = btn.dataset.code;
      const isPressed = btn.getAttribute("aria-pressed") === "true";
      // flip
      await sendActuator(code, !isPressed);
    });
  });

  // clear logs (calls an endpoint not provided — here just local)
  const clearBtn = $id("btn-clear-logs");
  if (clearBtn) {
    clearBtn.addEventListener("click", async () => {
      if (!confirm("Bersihkan log dari database?")) return;
      try {
        // simple: call update endpoint that clears logs (not implemented server-side)
        // We'll implement inline quick endpoint call here if desired.
        // For now we call a simple API: api/clear_logs.php (not created) -> safe fallback:
        const res = await fetch("api/clear_logs.php", { method: "POST" }).catch(
          () => null
        );
        showToast("Permintaan bersihkan log terkirim (jika endpoint tersedia)");
        fetchAndUpdate();
      } catch (e) {
        console.error(e);
        showToast("Gagal", "error");
      }
    });
  }
}

// init everything
window.addEventListener("load", () => {
  initCharts();
  initButtons();
  fetchAndUpdate();
  // polling
  setInterval(fetchAndUpdate, 4000);
});
