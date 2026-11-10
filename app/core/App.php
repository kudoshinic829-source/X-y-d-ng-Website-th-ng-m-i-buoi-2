<?php
class App
{
    protected string $controllerName;
    protected string $action;
    protected array $params = [];

    public function __construct()
    {
        $url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
        $segments = $url === '' ? [] : explode('/', $url);

        if (($segments[0] ?? '') === 'admin') {
            $this->controllerName = 'Admin' . ucfirst($segments[1] ?? 'dashboard') . 'Controller';
            $this->action = $segments[2] ?? 'index';
            $this->params = array_slice($segments, 3);
        } else {
            $this->controllerName = ucfirst($segments[0] ?? 'home') . 'Controller';
            $this->action = $segments[1] ?? 'index';
            $this->params = array_slice($segments, 2);
        }

        $this->dispatch();
    }

    protected function dispatch(): void
    {
        if (!class_exists($this->controllerName)) {
            $this->abort404('Không tìm thấy controller: ' . $this->controllerName);
        }

        $controller = new $this->controllerName();

        if (!method_exists($controller, $this->action) || str_starts_with($this->action, '_')) {
            $this->abort404('Không tìm thấy action: ' . $this->action);
        }

        call_user_func_array([$controller, $this->action], $this->params);
    }

    protected function abort404(string $debugMessage = ''): void
    {
        http_response_code(404);
        if ($debugMessage !== '') {
            error_log($debugMessage);
        }
        require __DIR__ . '/../views/errors/404.php';
        exit;
    }
}
