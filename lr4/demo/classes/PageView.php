<?php

class PageView extends View
{
    private string $pageTitle = '';

    public function setTitle(string $title): void
    {
        $this->pageTitle = $title;
    }

    public function render(string $viewFile, array $data = []): void
    {
        $filePath = $this->viewsDir . '/' . $viewFile . '.php';

        if (!file_exists($filePath)) {
            throw new RuntimeException("View не знайдено: {$viewFile}");
        }

        extract($data);

        ob_start();
        require $filePath;
        $content = ob_get_clean();

        $pageTitle = $this->pageTitle;

        require $this->viewsDir . '/layout/header.php';
        echo $content;
        require $this->viewsDir . '/layout/footer.php';
    }
}
