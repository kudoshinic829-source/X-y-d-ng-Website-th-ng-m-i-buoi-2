<?php
class AdminUserController extends AdminController
{
    public function index(): void
    {
        $users = User::all('created_at DESC');
        $this->view('admin/user/index', [
            'pageTitle' => 'Quản lý người dùng',
            'users' => $users
        ]);
    }

    public function role(): void
    {
        if (Auth::role() !== 'admin') {
            http_response_code(403);
            require __DIR__ . '/../../views/errors/403.php';
            exit;
        }

        verify_csrf($_POST['csrf_token'] ?? '');
        $userId = (int) ($_POST['user_id'] ?? 0);
        $newRole = $_POST['role'] ?? '';

        if ($userId === Auth::id()) {
            flash('error', 'Không thể tự đổi vai trò của chính mình.');
            $this->redirect('admin/user');
        }

        if (!in_array($newRole, ['admin', 'staff', 'customer'], true) || !User::find($userId)) {
            flash('error', 'Vai trò hoặc người dùng không hợp lệ.');
            $this->redirect('admin/user');
        }

        User::update($userId, ['role' => $newRole]);
        flash('success', 'Đã cập nhật vai trò người dùng.');
        $this->redirect('admin/user');
    }

    public function status(): void
    {
        verify_csrf($_POST['csrf_token'] ?? '');
        $userId = (int) ($_POST['user_id'] ?? 0);
        $newStatus = $_POST['status'] ?? '';

        if ($userId === Auth::id()) {
            flash('error', 'Không thể tự khoá tài khoản của chính mình.');
            $this->redirect('admin/user');
        }

        if (!in_array($newStatus, ['active', 'locked'], true) || !User::find($userId)) {
            flash('error', 'Trạng thái hoặc người dùng không hợp lệ.');
            $this->redirect('admin/user');
        }

        User::update($userId, ['status' => $newStatus]);
        flash('success', 'Đã cập nhật trạng thái tài khoản.');
        $this->redirect('admin/user');
    }
}
