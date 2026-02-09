<?php

class PageController extends Controller
{
    protected function render(string $viewFile, array $data = [], string $title = ''): void
    {
        if ($title !== '') {
            $this->view->setTitle($title);
        }
        $this->view->render($viewFile, $data);
    }
}
