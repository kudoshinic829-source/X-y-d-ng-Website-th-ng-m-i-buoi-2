<?php
abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    protected static function db(): PDO
    {
        return Database::getInstance();
    }

    public static function all(string $orderBy = ''): array
    {
        $allowedOrder = ['created_at DESC', 'id DESC', 'id ASC', 'full_name ASC'];
        $sql = 'SELECT * FROM ' . static::$table;
        if (in_array($orderBy, $allowedOrder, true)) {
            $sql .= ' ORDER BY ' . $orderBy;
        }
        return static::db()->query($sql)->fetchAll();
    }

    public static function find($id): ?array
    {
        $stmt = static::db()->prepare('SELECT * FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public static function delete($id): bool
    {
        $stmt = static::db()->prepare('DELETE FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = :id');
        return $stmt->execute(['id' => $id]);
    }

    public static function insert(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $columns);
        $sql = 'INSERT INTO ' . static::$table . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $stmt = static::db()->prepare($sql);
        $stmt->execute($data);
        return (int) static::db()->lastInsertId();
    }

    public static function update($id, array $data): bool
    {
        if (!$data) {
            return false;
        }
        $sets = array_map(fn($c) => $c . ' = :' . $c, array_keys($data));
        $sql = 'UPDATE ' . static::$table . ' SET ' . implode(', ', $sets) . ' WHERE ' . static::$primaryKey . ' = :pk_value';
        $data['pk_value'] = $id;
        return static::db()->prepare($sql)->execute($data);
    }

    public static function count(): int
    {
        return (int) static::db()->query('SELECT COUNT(*) FROM ' . static::$table)->fetchColumn();
    }
}
