<?php

class Router
{
    private string $defaultController = 'index';
    private string $defaultAction = 'main';

    public function parseRoute(): array
    {
        $route = $_GET['route'] ?? $this->defaultController . '/' . $this->defaultAction;
        $route = trim($route, '/');
        $parts = explode('/', $route);

        $controller = $parts[0] ?? $this->defaultController;
        $action = $parts[1] ?? $this->defaultAction;

        if (!preg_match('/^[a-z][a-z0-9]*$/i', $controller) || !preg_match('/^[a-z][a-z0-9_]*$/i', $action)) {
            $controller = $this->defaultController;
            $action = $this->defaultAction;
        }

        return [
            'controller' => $controller,
            'action' => $action,
        ];
    }
}
