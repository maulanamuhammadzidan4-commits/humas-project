<?php

final class KontakRepository
{
    public static function getAll(mysqli $connection): array
    {
        return RepositoryQuery::fetchAll(
            $connection,
            'SELECT id_kontak, nama, email, subjek, pesan, tanggal_kirim, status FROM kontak ORDER BY tanggal_kirim DESC'
        );
    }

    public static function markAsRead(mysqli $connection, int $id): bool
    {
        return RepositoryQuery::execute(
            $connection,
            "UPDATE kontak SET status = 'Sudah Dibaca' WHERE id_kontak = ?",
            'i',
            [$id]
        );
    }

    public static function create(mysqli $connection, array $data): bool
    {
        return RepositoryQuery::execute(
            $connection,
            'INSERT INTO kontak (nama, email, subjek, pesan) VALUES (?, ?, ?, ?)',
            'ssss',
            [$data['nama'], $data['email'], $data['subjek'], $data['pesan']]
        );
    }
}