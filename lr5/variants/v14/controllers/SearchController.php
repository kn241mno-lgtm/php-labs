<?php

require_once __DIR__ . '/PageController.php';
require_once __DIR__ . '/../classes/DbHelpers.php';

class SearchController extends PageController
{
    public function action_main(): void
    {
        $q = trim($this->request->get('q', ''));
        $results = [];
        if ($q !== '') {
            $results = DbHelpers::searchAll($q);
        }

        $this->render('search/results', ['query' => $q, 'results' => $results], 'Пошук');
    }
}
