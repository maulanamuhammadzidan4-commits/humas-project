<?php

final class SiswaRepository
{
    private const CLASS_MAJORS = ['AT', 'TJKT', 'PPLG', 'TE'];

    public static function getClassOptions(): array
    {
        $classes = [];
        foreach (self::CLASS_MAJORS as $major) {
            for ($number = 1; $number <= 5; $number++) {
                $classes[] = "XII {$major} {$number}";
            }
        }

        return $classes;
    }

    public static function countWithSearch(mysqli $connection, string $search): int
    {
        $where = $search === '' ? '' : ' WHERE nisn LIKE ? OR nama_siswa LIKE ? OR kelas LIKE ? OR jurusan LIKE ?';
        $row = RepositoryQuery::fetchOne(
            $connection,
            "SELECT COUNT(*) AS total FROM siswa$where",
            $search === '' ? '' : 'ssss',
            $search === '' ? [] : array_fill(0, 4, RepositoryQuery::likePattern($search))
        );

        return (int)($row['total'] ?? 0);
    }

    public static function getPaginated(mysqli $connection, string $search, int $limit, int $offset): array
    {
        $where = $search === '' ? '' : ' WHERE nisn LIKE ? OR nama_siswa LIKE ? OR kelas LIKE ? OR jurusan LIKE ?';
        $types = $search === '' ? 'ii' : 'ssssii';
        $params = $search === ''
            ? [$limit, $offset]
            : array_merge(array_fill(0, 4, RepositoryQuery::likePattern($search)), [$limit, $offset]);

        return RepositoryQuery::fetchAll(
            $connection,
            "SELECT id, nisn, nama_siswa, kelas, jurusan, status_alumni,
                    created_at, updated_at
             FROM siswa$where
             ORDER BY created_at DESC
             LIMIT ? OFFSET ?",
            $types,
            $params
        );
    }

    public static function getOptions(mysqli $connection): array
    {
        return RepositoryQuery::fetchAll(
            $connection,
            'SELECT id, nisn, nama_siswa, kelas, jurusan FROM siswa ORDER BY nama_siswa ASC'
        );
    }

    public static function findByName(mysqli $connection, string $name): ?array
    {
        return RepositoryQuery::fetchOne($connection, 'SELECT id FROM siswa WHERE nama_siswa = ? LIMIT 1', 's', [$name]);
    }

    public static function findUniqueByName(mysqli $connection, string $name): ?array
    {
        $matches = RepositoryQuery::fetchAll($connection, 'SELECT id FROM siswa WHERE nama_siswa = ? LIMIT 2', 's', [$name]);
        if (count($matches) > 1) {
            throw new InvalidArgumentException('Nama siswa tidak unik; pastikan hanya ada satu siswa dengan nama tersebut.');
        }

        return $matches[0] ?? null;
    }

    public static function findByNisn(mysqli $connection, string $nisn): ?array
    {
        return RepositoryQuery::fetchOne($connection, 'SELECT id FROM siswa WHERE nisn = ? LIMIT 1', 's', [$nisn]);
    }

    public static function findByNisnExcept(mysqli $connection, string $nisn, int $excludeId): ?array
    {
        return RepositoryQuery::fetchOne(
            $connection,
            'SELECT id FROM siswa WHERE nisn = ? AND id != ? LIMIT 1',
            'si',
            [$nisn, $excludeId]
        );
    }

    public static function create(mysqli $connection, array $data): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'INSERT INTO siswa (nisn, nama_siswa, kelas, jurusan, status_alumni) VALUES (?, ?, ?, ?, ?)',
            'ssssi',
            [$data['nisn'], $data['nama_siswa'], $data['kelas'], $data['jurusan'], $data['status_alumni']]
        );
    }

    public static function update(mysqli $connection, int $id, array $data): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'UPDATE siswa SET nisn = ?, nama_siswa = ?, kelas = ?, jurusan = ?, status_alumni = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
            'ssssii',
            [$data['nisn'], $data['nama_siswa'], $data['kelas'], $data['jurusan'], $data['status_alumni'], $id]
        );
    }

    public static function delete(mysqli $connection, int $id): bool
    {
        return RepositoryQuery::execute($connection, 'DELETE FROM siswa WHERE id = ?', 'i', [$id]);
    }
}