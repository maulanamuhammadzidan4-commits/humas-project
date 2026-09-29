<?php

final class RepositoryQuery
{
    private static function prepare(mysqli $connection, string $sql, string $types, array $params): mysqli_stmt
    {
        $statement = mysqli_prepare($connection, $sql);

        if ($types !== '') {
            $arguments = [$statement, $types];
            foreach ($params as $index => $value) {
                $arguments[] = &$params[$index];
            }
            call_user_func_array('mysqli_stmt_bind_param', $arguments);
        }

        return $statement;
    }

    public static function fetchAll(mysqli $connection, string $sql, string $types = '', array $params = []): array
    {
        $statement = self::prepare($connection, $sql, $types, $params);
        try {
            mysqli_stmt_execute($statement);
            $result = mysqli_stmt_get_result($statement);
            return $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
        } finally {
            mysqli_stmt_close($statement);
        }
    }

    public static function fetchOne(mysqli $connection, string $sql, string $types = '', array $params = []): ?array
    {
        $rows = self::fetchAll($connection, $sql, $types, $params);
        return $rows[0] ?? null;
    }

    public static function execute(mysqli $connection, string $sql, string $types = '', array $params = []): bool
    {
        $statement = self::prepare($connection, $sql, $types, $params);
        try {
            return mysqli_stmt_execute($statement);
        } finally {
            mysqli_stmt_close($statement);
        }
    }

    public static function executeAffectedRows(mysqli $connection, string $sql, string $types = '', array $params = []): int
    {
        $statement = self::prepare($connection, $sql, $types, $params);
        try {
            mysqli_stmt_execute($statement);
            return mysqli_stmt_affected_rows($statement);
        } finally {
            mysqli_stmt_close($statement);
        }
    }
}