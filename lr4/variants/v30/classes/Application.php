<?php

class Application
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function run(): void
    {
        $route = $this->router->parseRoute();

        $controllerName = ucfirst($route['controller']) . 'Controller';
        $actionName = 'action_' . $route['action'];

        if (!class_exists($controllerName)) {
            $this->show404("Контролер '{$route['controller']}' не знайдено.");
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $actionName)) {
            $this->show404("Дію '{$route['action']}' не знайдено в контролері '{$route['controller']}'.");
            return;
        }

        $controller->$actionName();
    }

    private function show404(string $message): void
    {
        http_response_code(404);
        $view = new PageView();
        $view->setTitle('404 — Сторінку не знайдено');
        $view->render('layout/404', ['message' => $message]);
    }
}
