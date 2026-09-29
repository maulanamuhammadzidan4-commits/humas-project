<?php

final class DashboardRepository
{
    public static function getStats(mysqli $connection): array
    {
        $row = RepositoryQuery::fetchOne(
            $connection,
            "SELECT
                (SELECT COUNT(*) FROM perusahaan) AS perusahaan,
                (SELECT COUNT(*) FROM perusahaan WHERE status_mou IS NOT NULL AND status_mou != '') AS mou_aktif,
                (SELECT COUNT(*) FROM lowongan_kerja) AS lowongan,
                (SELECT COUNT(*) FROM lowongan_kerja WHERE status_loker = 'Buka') AS loker_buka,
                (SELECT COUNT(*) FROM siswa) AS siswa,
                (SELECT COUNT(*) FROM siswa WHERE status_alumni = 1) AS alumni,
                (SELECT COUNT(*) FROM pkl_penempatan) AS pkl,
                (SELECT COUNT(*) FROM pkl_penempatan WHERE status_penempatan = 'Disetujui') AS pkl_aktif,
                (SELECT COUNT(*) FROM tracer_study) AS tracer,
                (SELECT COUNT(*) FROM users) AS users"
        );

        return array_map('intval', $row ?? []);
    }

    public static function getExpiringLowongan(mysqli $connection, int $limit = 5): array
    {
        return RepositoryQuery::fetchAll(
            $connection,
                "SELECT lk.judul_posisi AS posisi, p.nama_perusahaan AS perusahaan,
                    lk.batas_pendaftaran AS batas_daftar, lk.status_loker
             FROM lowongan_kerja lk
             INNER JOIN perusahaan p ON p.id = lk.perusahaan_id
             WHERE lk.status_loker = 'Buka'
               AND lk.batas_pendaftaran BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
             ORDER BY lk.batas_pendaftaran ASC
             LIMIT ?",
            'i',
            [$limit]
        );
    }

    public static function getRecentPkl(mysqli $connection, int $limit = 6): array
    {
        return RepositoryQuery::fetchAll(
            $connection,
            "SELECT s.nama_siswa AS siswa, p.nama_perusahaan AS perusahaan,
                    pk.pembimbing_guru, pk.status_penempatan,
                    pk.tanggal_mulai, pk.tanggal_selesai
             FROM pkl_penempatan pk
             INNER JOIN siswa s ON s.id = pk.siswa_id
             INNER JOIN perusahaan p ON p.id = pk.perusahaan_id
             ORDER BY pk.created_at DESC
             LIMIT ?",
            'i',
            [$limit]
        );
    }

    public static function getTracerDistribution(mysqli $connection): array
    {
        $rows = RepositoryQuery::fetchAll(
            $connection,
            'SELECT status_alumni, COUNT(*) AS n FROM tracer_study GROUP BY status_alumni'
        );

        $distribution = [];
        foreach ($rows as $row) {
            $distribution[$row['status_alumni']] = (int)$row['n'];
        }

        return $distribution;
    }
}