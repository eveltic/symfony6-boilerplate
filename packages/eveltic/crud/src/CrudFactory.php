<?php

namespace Eveltic\Crud;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CrudFactory
{
    private Request $request;
    public function __construct(RequestStack $requestStack)
    {
        $this->request = $requestStack->getMainRequest();
        dump($this);
    }
}
