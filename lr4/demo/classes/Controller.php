<?php

class Controller
{
    protected Request $request;
    protected PageView $view;

    public function __construct()
    {
        $this->request = new Request();
        $this->view = new PageView();
    }

    public function redirect(string $route): void
    {
        header('Location: index.php?route=' . $route);
        exit;
    }
}
