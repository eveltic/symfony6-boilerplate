<?php

namespace Eveltic\Crud\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Eveltic\Crud\Contracts\CrudControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractCrudController extends AbstractController// implements CrudControllerInterface
{
    #[Route('/edit', name: 'edit')]
    public function edit(): Response
    {
        return new Response('<!DOCTYPE html><html><head></head><body>Eveltic Crud Edit controller</body></html>');
    }
}
