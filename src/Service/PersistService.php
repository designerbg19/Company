<?php

namespace App\Service;

use App\Controller\MainController;
use Symfony\Component\HttpFoundation\Request;

class PersistService extends MainController
{
    public function insert(Request $request,$entityType,$param,$data)
    {
        $form = $this->createForm($entityType, $param);
        $form->handleRequest($request);
        $form->submit($data);
    }

    public function update(Request $request,$entityType,$param,$data)
    {
        $form = $this->createForm($entityType, $param);
        $form->handleRequest($request);
        $form->submit($data,false);
    }

}