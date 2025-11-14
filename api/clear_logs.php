<?php
// =============================
// File: api/clear_logs.php
// Fungsi: Menghapus seluruh riwayat data sensor di database
// =============================

include '../config/db.php';

// Query hapus semua data di tabel sensor_data
$query = "DELETE FROM log_aktivitas";

if ($conn->query($query) === TRUE) {
    echo "Riwayat data sensor berhasil dihapus!";
} else {
    echo "Gagal menghapus data: " . $conn->error;
}

// Tutup koneksi
$conn->close();
?>
