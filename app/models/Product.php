<?php
class Product extends Model
{
    protected static string $table = 'products';

    public static function getHomeProducts(): array
    {
        $sql = "SELECT p.*, c.name AS category_name, pi.file_name AS image
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id
                LEFT JOIN product_images pi ON pi.product_id = p.id AND pi.is_primary = 1
                WHERE p.status = 'active'
                ORDER BY p.is_featured DESC, p.created_at DESC";
        return self::db()->query($sql)->fetchAll();
    }
}
