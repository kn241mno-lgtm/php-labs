<?php

class View
{
    protected string $viewsDir;

    public function __construct()
    {
        $this->viewsDir = VIEWS_DIR;
    }

    public function render(string $viewFile, array $data = []): void
    {
        $filePath = $this->viewsDir . '/' . $viewFile . '.php';

        if (!file_exists($filePath)) {
            throw new RuntimeException("View не знайдено: {$viewFile}");
        }

        extract($data);
        require $filePath;
    }
}
