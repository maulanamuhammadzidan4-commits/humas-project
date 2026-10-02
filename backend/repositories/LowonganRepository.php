<?php

final class LowonganRepository
{
    public static function countWithSearch(mysqli $connection, string $search): int
    {
        $where = $search === '' ? '' : ' WHERE lk.judul_posisi LIKE ? OR p.nama_perusahaan LIKE ?';
        $row = RepositoryQuery::fetchOne(
            $connection,
            "SELECT COUNT(*) AS total
             FROM lowongan_kerja lk
             INNER JOIN perusahaan p ON p.id = lk.perusahaan_id$where",
            $search === '' ? '' : 'ss',
            $search === '' ? [] : array_fill(0, 2, RepositoryQuery::likePattern($search))
        );

        return (int)($row['total'] ?? 0);
    }

    public static function getPaginated(mysqli $connection, string $search, int $limit, int $offset): array
    {
        $where = $search === '' ? '' : ' WHERE lk.judul_posisi LIKE ? OR p.nama_perusahaan LIKE ?';
        $types = $search === '' ? 'ii' : 'ssii';
        $params = $search === ''
            ? [$limit, $offset]
            : [RepositoryQuery::likePattern($search), RepositoryQuery::likePattern($search), $limit, $offset];

        return RepositoryQuery::fetchAll(
            $connection,
            "SELECT lk.id, lk.perusahaan_id, lk.judul_posisi,
                    lk.deskripsi_pekerjaan, lk.kuota, lk.batas_pendaftaran,
                    lk.status_loker, lk.created_at, lk.updated_at,
                    p.nama_perusahaan
             FROM lowongan_kerja lk
             INNER JOIN perusahaan p ON p.id = lk.perusahaan_id$where
             ORDER BY lk.created_at DESC
             LIMIT ? OFFSET ?",
            $types,
            $params
        );
    }

    public static function create(mysqli $connection, array $data): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'INSERT INTO lowongan_kerja (perusahaan_id, judul_posisi, deskripsi_pekerjaan, kuota, batas_pendaftaran, status_loker) VALUES (?, ?, ?, ?, ?, ?)',
            'ississ',
            [$data['perusahaan_id'], $data['judul_posisi'], $data['deskripsi_pekerjaan'], $data['kuota'], $data['batas_pendaftaran'], $data['status_loker']]
        );
    }

    public static function update(mysqli $connection, int $id, array $data): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'UPDATE lowongan_kerja SET perusahaan_id = ?, judul_posisi = ?, deskripsi_pekerjaan = ?, kuota = ?, batas_pendaftaran = ?, status_loker = ? WHERE id = ?',
            'ississi',
            [$data['perusahaan_id'], $data['judul_posisi'], $data['deskripsi_pekerjaan'], $data['kuota'], $data['batas_pendaftaran'], $data['status_loker'], $id]
        );
    }

    public static function delete(mysqli $connection, int $id): int
    {
        return RepositoryQuery::executeAffectedRows(
            $connection,
            'DELETE FROM lowongan_kerja WHERE id = ?',
            'i',
            [$id]
        );
    }
}