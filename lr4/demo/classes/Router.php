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

        return [
            'controller' => $parts[0] ?? $this->defaultController,
            'action' => $parts[1] ?? $this->defaultAction,
        ];
    }
}
