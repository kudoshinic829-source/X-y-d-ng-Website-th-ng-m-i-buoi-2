<?php
class AdminAuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check() && in_array(Auth::role(), ['admin', 'staff'], true)) {
            $this->redirect('admin/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf($_POST['csrf_token'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $user = User::findByEmail($email);
            $isStaffAccount = $user && in_array($user['role'], ['admin', 'staff'], true);

            if (!$user || !$isStaffAccount || !password_verify($password, $user['password'])) {
                flash('error', 'Thông tin đăng nhập không đúng hoặc tài khoản không có quyền quản trị.');
                $this->redirect('admin/auth/login');
            }

            if ($user['status'] !== 'active') {
                flash('error', 'Tài khoản của bạn đã bị khoá.');
                $this->redirect('admin/auth/login');
            }

            Auth::login($user);
            $this->redirect('admin/dashboard');
        }

        $this->view('admin/login', ['pageTitle' => 'Đăng nhập quản trị'], false);
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('admin/auth/login');
    }
}
