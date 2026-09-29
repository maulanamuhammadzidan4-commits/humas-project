<?php

final class BeritaRepository
{
    public static function findById(mysqli $connection, int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        return RepositoryQuery::fetchOne(
            $connection,
            'SELECT * FROM berita WHERE id_berita = ? LIMIT 1',
            'i',
            [$id]
        );
    }
}