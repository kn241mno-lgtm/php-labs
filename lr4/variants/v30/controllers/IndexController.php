<?php

class IndexController extends PageController
{
    public function action_main(): void
    {
        $this->render('index/main', [], 'Головна');
    }
}
