<?php

namespace Eveltic\Crud\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CrudController extends AbstractController
{
    #[Route('/edit', name: 'edit')]
    public function edit(): Response
    {
        return new Response('<!DOCTYPE html><html><head></head><body>Eveltic Crud Edit controller</body></html>');
    }
}
