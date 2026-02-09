<?php

class ReqviewController extends PageController
{
    public function action_showrequest(): void
    {
        $getParams = $this->request->allGet();
        $postParams = $this->request->allPost();

        unset($getParams['route']);

        $this->render('reqview/showrequest', [
            'getParams' => $getParams,
            'postParams' => $postParams,
            'method' => $this->request->method(),
        ], 'Перегляд параметрів запиту');
    }
}
