<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/core/helpers.php';
require_once __DIR__ . '/../app/core/Database.php';

if (!extension_loaded('gd')) {
    die('PHP chưa bật extension GD.');
}

$pdo = Database::getInstance();
$products = $pdo->query('SELECT id, name FROM products ORDER BY id')->fetchAll();
$skipProductIds = [3, 17];
$uploadDir = __DIR__ . '/../public/uploads/products/';
$thumbDir = $uploadDir . 'thumbs/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);
if (!is_dir($thumbDir)) mkdir($thumbDir, 0775, true);

$palette = [[52, 120, 190], [40, 160, 130], [190, 100, 80], [120, 90, 170], [210, 150, 50]];
$stmt = $pdo->prepare('INSERT INTO product_images (product_id, file_name, original_name, alt_text, is_primary, sort_order, created_at) VALUES (:product_id, :file_name, :original_name, :alt_text, :is_primary, :sort_order, NOW())');

foreach ($products as $product) {
    if (in_array((int)$product['id'], $skipProductIds, true)) {
        echo 'Bỏ qua sản phẩm #' . (int)$product['id'] . ' để kiểm tra ảnh mặc định.<br>';
        continue;
    }

    $count = random_int(1, 3);
    for ($i = 0; $i < $count; $i++) {
        $fileName = uniqid('seed_', true) . '.jpg';
        [$r, $g, $b] = $palette[array_rand($palette)];
        $main = imagecreatetruecolor(600, 600);
        $bg = imagecolorallocate($main, $r, $g, $b);
        imagefill($main, 0, 0, $bg);
        $white = imagecolorallocate($main, 255, 255, 255);
        imagestring($main, 5, 25, 290, substr($product['name'], 0, 45), $white);
        imagejpeg($main, $uploadDir . $fileName, 85);

        $thumb = imagecreatetruecolor(300, 300);
        imagecopyresampled($thumb, $main, 0, 0, 0, 0, 300, 300, 600, 600);
        imagejpeg($thumb, $thumbDir . $fileName, 85);
        imagedestroy($main);
        imagedestroy($thumb);

        $stmt->execute([
            'product_id' => $product['id'],
            'file_name' => $fileName,
            'original_name' => 'seed.jpg',
            'alt_text' => $product['name'],
            'is_primary' => $i === 0 ? 1 : 0,
            'sort_order' => $i
        ]);
    }
    echo 'Đã tạo ' . $count . ' ảnh cho ' . e($product['name']) . '<br>';
}
echo '<p><strong>Hoàn tất sinh ảnh mẫu.</strong></p>';
