<?php
/**
 * Backend Data Provider — Dashboard Admin Humas SMK
 * Menyediakan data statistik, lowongan expiring, penempatan PKL, dan tracer study.
 */

if (!isset($koneksi)) {
    require_once __DIR__ . '/../../backend/connection.php';
}
require_once __DIR__ . '/../../backend/repositories/bootstrap.php';

$stats = DashboardRepository::getStats($koneksi);
$expiring = DashboardRepository::getExpiringLowongan($koneksi);
$pkl_terbaru = DashboardRepository::getRecentPkl($koneksi);
$tracer_data = DashboardRepository::getTracerDistribution($koneksi);

// Default value tracer study
$tracer_data['Bekerja']       = $tracer_data['Bekerja'] ?? 0;
$tracer_data['Kuliah']        = $tracer_data['Kuliah'] ?? 0;
$tracer_data['Wirausaha']     = $tracer_data['Wirausaha'] ?? 0;
$tracer_data['Mencari Kerja'] = $tracer_data['Mencari Kerja'] ?? 0;
$tracer_data['Menikah']       = $tracer_data['Menikah'] ?? 0;
