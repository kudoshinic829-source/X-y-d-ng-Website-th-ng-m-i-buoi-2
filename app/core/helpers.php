<?php
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function base_url(string $path = ''): string
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return base_url('assets/' . ltrim($path, '/'));
}

function format_vnd($number): string
{
    return number_format((float) $number, 0, ',', '.') . ' đ';
}

function product_image_url(?string $fileName, string $size = 'thumb'): string
{
    if (empty($fileName)) {
        return asset('img/no-image.png');
    }

    $folder = $size === 'full' ? 'uploads/products/' : 'uploads/products/thumbs/';
    $diskPath = __DIR__ . '/../../public/' . $folder . $fileName;

    if (!file_exists($diskPath)) {
        return asset('img/no-image.png');
    }

    return base_url($folder . $fileName);
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(string $token): void
{
    if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(419);
        die('Phiên làm việc đã hết hạn hoặc yêu cầu không hợp lệ. Vui lòng tải lại trang và thử lại.');
    }
}

function flash(string $key, ?string $message = null, string $type = 'success'): ?array
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = ['message' => $message, 'type' => $type];
        return null;
    }

    if (!empty($_SESSION['flash'][$key])) {
        $data = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $data;
    }

    return null;
}
