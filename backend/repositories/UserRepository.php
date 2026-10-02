<?php

final class UserRepository
{
    public static function countWithSearch(mysqli $connection, string $search): int
    {
        $where = $search === '' ? '' : ' WHERE username LIKE ? OR nama_lengkap LIKE ? OR jabatan LIKE ?';
        $row = RepositoryQuery::fetchOne(
            $connection,
            "SELECT COUNT(*) AS total FROM users$where",
            $search === '' ? '' : 'sss',
            $search === '' ? [] : array_fill(0, 3, RepositoryQuery::likePattern($search))
        );

        return (int)($row['total'] ?? 0);
    }

    public static function getPaginated(mysqli $connection, string $search, int $limit, int $offset): array
    {
        $where = $search === '' ? '' : ' WHERE username LIKE ? OR nama_lengkap LIKE ? OR jabatan LIKE ?';
        $types = $search === '' ? 'ii' : 'sssii';
        $params = $search === ''
            ? [$limit, $offset]
            : array_merge(array_fill(0, 3, RepositoryQuery::likePattern($search)), [$limit, $offset]);

        return RepositoryQuery::fetchAll(
            $connection,
            "SELECT * FROM users$where ORDER BY created_at DESC LIMIT ? OFFSET ?",
            $types,
            $params
        );
    }

    public static function findByUsername(mysqli $connection, string $username): ?array
    {
        return RepositoryQuery::fetchOne(
            $connection,
            'SELECT id_user, username, password, nama_lengkap, jabatan FROM users WHERE username = ? LIMIT 1',
            's',
            [$username]
        );
    }

    public static function usernameExistsExcept(mysqli $connection, string $username, int $excludeId = 0): bool
    {
        return RepositoryQuery::fetchOne(
            $connection,
            'SELECT id_user FROM users WHERE username = ? AND id_user != ? LIMIT 1',
            'si',
            [$username, $excludeId]
        ) !== null;
    }

    public static function updatePassword(mysqli $connection, int $id, string $passwordHash): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'UPDATE users SET password = ? WHERE id_user = ?',
            'si',
            [$passwordHash, $id]
        );
    }

    public static function create(mysqli $connection, array $data): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'INSERT INTO users (username, password, nama_lengkap, jabatan) VALUES (?, ?, ?, ?)',
            'ssss',
            [$data['username'], $data['password'], $data['nama_lengkap'], $data['jabatan']]
        );
    }

    public static function update(mysqli $connection, int $id, array $data): bool
    {
        if (isset($data['password'])) {
            return RepositoryQuery::execute(
                $connection,
                'UPDATE users SET username = ?, password = ?, nama_lengkap = ?, jabatan = ? WHERE id_user = ?',
                'ssssi',
                [$data['username'], $data['password'], $data['nama_lengkap'], $data['jabatan'], $id]
            );
        }

        return RepositoryQuery::execute(
            $connection,
            'UPDATE users SET username = ?, nama_lengkap = ?, jabatan = ? WHERE id_user = ?',
            'sssi',
            [$data['username'], $data['nama_lengkap'], $data['jabatan'], $id]
        );
    }

    public static function delete(mysqli $connection, int $id): bool
    {
        return RepositoryQuery::execute($connection, 'DELETE FROM users WHERE id_user = ?', 'i', [$id]);
    }
}