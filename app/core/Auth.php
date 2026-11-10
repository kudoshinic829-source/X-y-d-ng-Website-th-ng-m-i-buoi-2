<?php
class Auth
{
    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
    }

    public static function role(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            flash('error', 'Vui lòng đăng nhập để tiếp tục.');
            header('Location: ' . base_url('auth/login'));
            exit;
        }
    }

    public static function requireRole(array $roles): void
    {
        if (!self::check()) {
            flash('error', 'Vui lòng đăng nhập quản trị.');
            header('Location: ' . base_url('admin/auth/login'));
            exit;
        }

        if (!in_array(self::role(), $roles, true)) {
            http_response_code(403);
            require __DIR__ . '/../views/errors/403.php';
            exit;
        }
    }
}
