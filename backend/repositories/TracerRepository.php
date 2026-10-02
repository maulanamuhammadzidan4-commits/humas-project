<?php

final class TracerRepository
{
    public static function countWithSearch(mysqli $connection, string $search): int
    {
        $where = $search === '' ? '' : ' WHERE s.nama_siswa LIKE ? OR s.nisn LIKE ? OR s.kelas LIKE ? OR s.jurusan LIKE ? OR ts.nama_instansi LIKE ? OR ts.status_alumni LIKE ?';
        $row = RepositoryQuery::fetchOne(
            $connection,
            "SELECT COUNT(*) AS total
             FROM tracer_study ts
             INNER JOIN siswa s ON s.id = ts.siswa_id$where",
            $search === '' ? '' : 'ssssss',
            $search === '' ? [] : array_fill(0, 6, RepositoryQuery::likePattern($search))
        );

        return (int)($row['total'] ?? 0);
    }

    public static function getPaginated(mysqli $connection, string $search, int $limit, int $offset): array
    {
        $where = $search === '' ? '' : ' WHERE s.nama_siswa LIKE ? OR s.nisn LIKE ? OR s.kelas LIKE ? OR s.jurusan LIKE ? OR ts.nama_instansi LIKE ? OR ts.status_alumni LIKE ?';
        $types = $search === '' ? 'ii' : 'ssssssii';
        $params = $search === ''
            ? [$limit, $offset]
            : array_merge(array_fill(0, 6, RepositoryQuery::likePattern($search)), [$limit, $offset]);

        return RepositoryQuery::fetchAll(
            $connection,
            "SELECT ts.id, ts.siswa_id, ts.tahun_lulus, ts.status_alumni,
                    ts.nama_instansi, ts.pendapatan_bulanan, ts.created_at,
                    ts.updated_at, s.id AS id_siswa, s.nisn, s.nama_siswa,
                    s.kelas, s.jurusan, s.status_alumni AS status_siswa
             FROM tracer_study ts
             INNER JOIN siswa s ON s.id = ts.siswa_id$where
             ORDER BY ts.created_at DESC
             LIMIT ? OFFSET ?",
            $types,
            $params
        );
    }

    public static function getStatistics(mysqli $connection): array
    {
        $rows = RepositoryQuery::fetchAll(
            $connection,
            'SELECT status_alumni, COUNT(*) AS total FROM tracer_study GROUP BY status_alumni'
        );
        $stats = ['total' => 0, 'Bekerja' => 0, 'Kuliah' => 0, 'Wirausaha' => 0, 'Mencari Kerja' => 0, 'Menikah' => 0];

        foreach ($rows as $row) {
            $count = (int)$row['total'];
            $stats['total'] += $count;
            $status = strtolower((string) $row['status_alumni']) === 'menikah'
                ? 'Menikah'
                : $row['status_alumni'];
            if (array_key_exists($status, $stats)) {
                $stats[$status] += $count;
            }
        }

        return $stats;
    }

    public static function findByStudentId(mysqli $connection, int $studentId, int $excludeId = 0): ?array
    {
        return RepositoryQuery::fetchOne(
            $connection,
            'SELECT id FROM tracer_study WHERE siswa_id = ? AND id != ? LIMIT 1',
            'ii',
            [$studentId, $excludeId]
        );
    }

    public static function create(mysqli $connection, array $data): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'INSERT INTO tracer_study (siswa_id, tahun_lulus, status_alumni, nama_instansi, pendapatan_bulanan) VALUES (?, ?, ?, ?, ?)',
            'iissi',
            [$data['siswa_id'], $data['tahun_lulus'], $data['status_alumni'], $data['nama_instansi'], $data['pendapatan_bulanan']]
        );
    }

    public static function update(mysqli $connection, int $id, array $data): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'UPDATE tracer_study SET siswa_id = ?, tahun_lulus = ?, status_alumni = ?, nama_instansi = ?, pendapatan_bulanan = ?, updated_at = NOW() WHERE id = ?',
            'iissii',
            [$data['siswa_id'], $data['tahun_lulus'], $data['status_alumni'], $data['nama_instansi'], $data['pendapatan_bulanan'], $id]
        );
    }

    public static function delete(mysqli $connection, int $id): bool
    {
        return RepositoryQuery::execute($connection, 'DELETE FROM tracer_study WHERE id = ?', 'i', [$id]);
    }
}