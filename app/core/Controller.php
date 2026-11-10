<?php
abstract class Controller
{
    protected function view(string $view, array $data = [], string|false $layout = 'main'): void
    {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(500);
            die('Không tìm thấy view: ' . e($view));
        }

        if ($layout === false) {
            extract($data);
            require $viewFile;
            return;
        }

        $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';
        if (!file_exists($layoutFile)) {
            http_response_code(500);
            die('Không tìm thấy layout: ' . e($layout));
        }

        $content = function () use ($viewFile, $data) {
            extract($data);
            require $viewFile;
        };

        extract($data);
        require $layoutFile;
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . base_url($path));
        exit;
    }
}
