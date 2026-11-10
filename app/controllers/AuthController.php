<?php
class AuthController extends Controller
{
    public function register(): void
    {
        if (Auth::check()) {
            $this->redirect('');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf($_POST['csrf_token'] ?? '');

            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['password_confirm'] ?? '';
            $errors = [];

            if ($fullName === '') $errors[] = 'Họ tên không được để trống.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ.';
            if (User::findByEmail($email)) $errors[] = 'Email đã được sử dụng.';
            if (strlen($password) < 6) $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
            if ($password !== $confirm) $errors[] = 'Mật khẩu xác nhận không khớp.';

            if ($errors) {
                flash('error', implode('<br>', array_map('e', $errors)));
                $_SESSION['old'] = ['full_name' => $fullName, 'email' => $email];
                $this->redirect('auth/register');
            }

            $id = User::insert([
                'full_name' => $fullName,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'customer',
                'status' => 'active'
            ]);

            $user = User::find($id);
            Auth::login($user);
            flash('success', 'Đăng ký tài khoản thành công.');
            $this->redirect('');
        }

        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);
        $this->view('auth/register', ['pageTitle' => 'Đăng ký', 'old' => $old]);
    }

    public function login(): void
    {
        if (Auth::check()) $this->redirect('');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            verify_csrf($_POST['csrf_token'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $user = User::findByEmail($email);

            if (!$user || !password_verify($password, $user['password'])) {
                flash('error', 'Email hoặc mật khẩu không chính xác.');
                $this->redirect('auth/login');
            }

            if ($user['status'] !== 'active') {
                flash('error', 'Tài khoản của bạn đã bị khoá.');
                $this->redirect('auth/login');
            }

            Auth::login($user);
            flash('success', 'Đăng nhập thành công.');
            $this->redirect('');
        }

        $this->view('auth/login', ['pageTitle' => 'Đăng nhập']);
    }

    public function logout(): void
    {
        Auth::logout();
        flash('success', 'Bạn đã đăng xuất.');
        $this->redirect('');
    }
}
