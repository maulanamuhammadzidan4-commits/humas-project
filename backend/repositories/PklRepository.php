<?php

final class PklRepository
{
    public static function countWithSearch(mysqli $connection, string $search): int
    {
        $where = $search === '' ? '' : ' WHERE s.nama_siswa LIKE ? OR pr.nama_perusahaan LIKE ? OR pk.pembimbing_guru LIKE ? OR pk.status_penempatan LIKE ?';
        $params = $search === '' ? [] : array_fill(0, 4, RepositoryQuery::likePattern($search));
        $row = RepositoryQuery::fetchOne(
            $connection,
            "SELECT COUNT(*) AS total
             FROM pkl_penempatan pk
             LEFT JOIN siswa s ON pk.siswa_id = s.id
             LEFT JOIN perusahaan pr ON pk.perusahaan_id = pr.id$where",
            $search === '' ? '' : 'ssss',
            $params
        );

        return (int)($row['total'] ?? 0);
    }

    public static function getPaginated(mysqli $connection, string $search, int $limit, int $offset): array
    {
        $where = $search === '' ? '' : ' WHERE s.nama_siswa LIKE ? OR pr.nama_perusahaan LIKE ? OR pk.pembimbing_guru LIKE ? OR pk.status_penempatan LIKE ?';
        $types = $search === '' ? 'ii' : 'ssssii';
        $params = $search === ''
            ? [$limit, $offset]
            : array_merge(array_fill(0, 4, RepositoryQuery::likePattern($search)), [$limit, $offset]);

        return RepositoryQuery::fetchAll(
            $connection,
            "SELECT pk.id, pk.siswa_id, pk.perusahaan_id, s.nama_siswa,
                    pr.nama_perusahaan, pk.pembimbing_guru AS pembimbing,
                    pk.tanggal_mulai, pk.tanggal_selesai, pk.status_penempatan,
                    pk.created_at, pk.updated_at
             FROM pkl_penempatan pk
             LEFT JOIN siswa s ON pk.siswa_id = s.id
             LEFT JOIN perusahaan pr ON pk.perusahaan_id = pr.id$where
             ORDER BY pk.created_at DESC
             LIMIT ? OFFSET ?",
            $types,
            $params
        );
    }

    public static function create(mysqli $connection, array $data): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'INSERT INTO pkl_penempatan (siswa_id, perusahaan_id, pembimbing_guru, tanggal_mulai, tanggal_selesai, status_penempatan) VALUES (?, ?, ?, ?, ?, ?)',
            'iissss',
            [$data['siswa_id'], $data['perusahaan_id'], $data['pembimbing_guru'], $data['tanggal_mulai'], $data['tanggal_selesai'], $data['status_penempatan']]
        );
    }

    public static function update(mysqli $connection, int $id, array $data): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'UPDATE pkl_penempatan SET siswa_id = ?, perusahaan_id = ?, pembimbing_guru = ?, tanggal_mulai = ?, tanggal_selesai = ?, status_penempatan = ? WHERE id = ?',
            'iissssi',
            [$data['siswa_id'], $data['perusahaan_id'], $data['pembimbing_guru'], $data['tanggal_mulai'], $data['tanggal_selesai'], $data['status_penempatan'], $id]
        );
    }

    public static function delete(mysqli $connection, int $id): bool
    {
        return RepositoryQuery::execute($connection, 'DELETE FROM pkl_penempatan WHERE id = ?', 'i', [$id]);
    }
}