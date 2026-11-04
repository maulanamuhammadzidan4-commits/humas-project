<?php

final class PerusahaanMouRepository
{
    private const STATUSES = ['Proses', 'Aktif', 'Kadaluarsa'];

    public static function countWithSearch(mysqli $connection, string $search): int
    {
        $where = $search === '' ? '' : ' WHERE p.nama_perusahaan LIKE ? OR p.sektor_bidang LIKE ?';
        $row = RepositoryQuery::fetchOne(
            $connection,
            "SELECT COUNT(*) AS total FROM perusahaan AS p$where",
            $search === '' ? '' : 'ss',
            $search === '' ? [] : array_fill(0, 2, RepositoryQuery::likePattern($search))
        );

        return (int)($row['total'] ?? 0);
    }

    public static function getPaginated(mysqli $connection, string $search, int $limit, int $offset): array
    {
        if ($limit < 1 || $offset < 0) {
            throw new InvalidArgumentException('Batas dan offset paginasi tidak valid.');
        }

        $where = $search === '' ? '' : ' WHERE p.nama_perusahaan LIKE ? OR p.sektor_bidang LIKE ?';
        $types = $search === '' ? 'ii' : 'ssii';
        $params = $search === ''
            ? [$limit, $offset]
            : [
                RepositoryQuery::likePattern($search),
                RepositoryQuery::likePattern($search),
                $limit,
                $offset,
            ];

        return RepositoryQuery::fetchAll(
            $connection,
            "SELECT p.id, p.nama_perusahaan, p.sektor_bidang,
                    COALESCE(pm.status, 'Proses') AS status,
                    pm.file_path, pm.original_filename, pm.mime_type, pm.file_size,
                    pm.updated_at AS mou_updated_at
             FROM perusahaan AS p
             LEFT JOIN perusahaan_mou AS pm ON pm.perusahaan_id = p.id
             $where
             ORDER BY p.nama_perusahaan ASC
             LIMIT ? OFFSET ?",
            $types,
            $params
        );
    }

    public static function getByPerusahaanId(mysqli $connection, int $perusahaanId): ?array
    {
        if ($perusahaanId < 1) {
            throw new InvalidArgumentException('ID perusahaan tidak valid.');
        }

        return RepositoryQuery::fetchOne(
            $connection,
            "SELECT p.id AS perusahaan_id, p.nama_perusahaan,
                    COALESCE(pm.status, 'Proses') AS status,
                    pm.file_path, pm.original_filename, pm.mime_type, pm.file_size,
                    pm.updated_at AS mou_updated_at
             FROM perusahaan AS p
             LEFT JOIN perusahaan_mou AS pm ON pm.perusahaan_id = p.id
             WHERE p.id = ?
             LIMIT 1",
            'i',
            [$perusahaanId]
        );
    }

    public static function saveStatus(mysqli $connection, int $perusahaanId, string $status): bool
    {
        self::validateStatus($perusahaanId, $status);

        return RepositoryQuery::execute(
            $connection,
            'INSERT INTO perusahaan_mou (perusahaan_id, status)
             VALUES (?, ?)
             ON DUPLICATE KEY UPDATE status = VALUES(status)',
            'is',
            [$perusahaanId, $status]
        );
    }

    public static function saveStatusAndFile(
        mysqli $connection,
        int $perusahaanId,
        string $status,
        string $filePath,
        string $originalFilename,
        string $mimeType,
        int $fileSize
    ): bool {
        self::validateStatus($perusahaanId, $status);
        if (
            $filePath === ''
            || self::stringLength($filePath) > 255
            || trim($originalFilename) === ''
            || self::stringLength($originalFilename) > 255
            || trim($mimeType) === ''
            || self::stringLength($mimeType) > 100
            || $fileSize < 0
        ) {
            throw new InvalidArgumentException('Metadata dokumen MoU tidak valid.');
        }

        return RepositoryQuery::execute(
            $connection,
            'INSERT INTO perusahaan_mou
                (perusahaan_id, status, file_path, original_filename, mime_type, file_size)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                status = VALUES(status),
                file_path = VALUES(file_path),
                original_filename = VALUES(original_filename),
                mime_type = VALUES(mime_type),
                file_size = VALUES(file_size)',
            'issssi',
            [$perusahaanId, $status, $filePath, $originalFilename, $mimeType, $fileSize]
        );
    }

    private static function stringLength(string $value): int
    {
        if (function_exists('mb_strlen')) {
            return mb_strlen($value, 'UTF-8');
        }

        $length = preg_match_all('/./us', $value);
        if ($length === false) {
            throw new InvalidArgumentException('Metadata dokumen MoU harus berupa teks UTF-8 yang valid.');
        }

        return $length;
    }

    private static function validateStatus(int $perusahaanId, string $status): void
    {
        if ($perusahaanId < 1) {
            throw new InvalidArgumentException('ID perusahaan tidak valid.');
        }

        if (!in_array($status, self::STATUSES, true)) {
            throw new InvalidArgumentException('Status MoU tidak valid.');
        }
    }
}
