<?php
abstract class AdminController extends Controller
{
    public function __construct()
    {
        Auth::requireRole(['admin', 'staff']);
    }

    protected function view(string $view, array $data = [], string|false $layout = 'admin'): void
    {
        parent::view($view, $data, $layout);
    }
}
