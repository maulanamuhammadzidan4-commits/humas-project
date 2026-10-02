<?php

final class PerusahaanRepository
{
    public static function countWithSearch(mysqli $connection, string $search): int
    {
        $where = $search === '' ? '' : ' WHERE nama_perusahaan LIKE ? OR sektor_bidang LIKE ?';
        $row = RepositoryQuery::fetchOne(
            $connection,
            "SELECT COUNT(*) AS total FROM perusahaan$where",
            $search === '' ? '' : 'ss',
            $search === '' ? [] : array_fill(0, 2, RepositoryQuery::likePattern($search))
        );

        return (int)($row['total'] ?? 0);
    }

    public static function getPaginated(mysqli $connection, string $search, int $limit, int $offset): array
    {
        $where = $search === '' ? '' : ' WHERE nama_perusahaan LIKE ? OR sektor_bidang LIKE ?';
        $types = $search === '' ? 'ii' : 'ssii';
        $params = $search === ''
            ? [$limit, $offset]
            : [RepositoryQuery::likePattern($search), RepositoryQuery::likePattern($search), $limit, $offset];

        return RepositoryQuery::fetchAll(
            $connection,
            "SELECT * FROM perusahaan$where ORDER BY created_at DESC LIMIT ? OFFSET ?",
            $types,
            $params
        );
    }

    public static function getOptions(mysqli $connection): array
    {
        return RepositoryQuery::fetchAll(
            $connection,
            'SELECT id, nama_perusahaan FROM perusahaan ORDER BY nama_perusahaan ASC'
        );
    }

    public static function findById(mysqli $connection, int $id): ?array
    {
        return RepositoryQuery::fetchOne($connection, 'SELECT id FROM perusahaan WHERE id = ? LIMIT 1', 'i', [$id]);
    }

    public static function findByName(mysqli $connection, string $name): ?array
    {
        return RepositoryQuery::fetchOne($connection, 'SELECT id FROM perusahaan WHERE nama_perusahaan = ? LIMIT 1', 's', [$name]);
    }

    public static function findUniqueByName(mysqli $connection, string $name): ?array
    {
        $matches = RepositoryQuery::fetchAll($connection, 'SELECT id FROM perusahaan WHERE nama_perusahaan = ? LIMIT 2', 's', [$name]);
        if (count($matches) > 1) {
            throw new InvalidArgumentException('Nama perusahaan tidak unik; pastikan hanya ada satu perusahaan dengan nama tersebut.');
        }

        return $matches[0] ?? null;
    }

    public static function create(mysqli $connection, array $data): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'INSERT INTO perusahaan (nama_perusahaan, sektor_bidang, jurusan, alamat, penanggung_jawab, no_telepon, status_mou) VALUES (?, ?, ?, ?, ?, ?, ?)',
            'sssssss',
            [$data['nama_perusahaan'], $data['sektor_bidang'], $data['jurusan'], $data['alamat'], $data['penanggung_jawab'], $data['no_telepon'], $data['status_mou']]
        );
    }

    public static function update(mysqli $connection, int $id, array $data): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'UPDATE perusahaan SET nama_perusahaan = ?, sektor_bidang = ?, jurusan = ?, alamat = ?, penanggung_jawab = ?, no_telepon = ?, status_mou = ? WHERE id = ?',
            'sssssssi',
            [$data['nama_perusahaan'], $data['sektor_bidang'], $data['jurusan'], $data['alamat'], $data['penanggung_jawab'], $data['no_telepon'], $data['status_mou'], $id]
        );
    }

    public static function delete(mysqli $connection, int $id): bool
    {
        return RepositoryQuery::execute($connection, 'DELETE FROM perusahaan WHERE id = ?', 'i', [$id]);
    }
}