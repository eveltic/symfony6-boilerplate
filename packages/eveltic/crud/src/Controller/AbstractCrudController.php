<?php

namespace Eveltic\Crud\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Eveltic\Crud\Contracts\CrudControllerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractCrudController extends AbstractController// implements CrudControllerInterface
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return new Response(sprintf('<!DOCTYPE html><html><head></head><body>Eveltic Crud Controller <strong>%s</strong></body></html>', __FUNCTION__));
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function new(): Response
    {
        return new Response(sprintf('<!DOCTYPE html><html><head></head><body>Eveltic Crud Controller <strong>%s</strong></body></html>', __FUNCTION__));
    }

    #[Route('/{id}/read', name: 'read', methods: ['GET'])]
    public function read(): Response
    {
        return new Response(sprintf('<!DOCTYPE html><html><head></head><body>Eveltic Crud Controller <strong>%s</strong></body></html>', __FUNCTION__));
    }

    #[Route('/{id}/update', name: 'update', methods: ['GET', 'POST'])]
    public function update(): Response
    {
        return new Response(sprintf('<!DOCTYPE html><html><head></head><body>Eveltic Crud Controller <strong>%s</strong></body></html>', __FUNCTION__));
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['GET', 'POST'])]
    public function delete(): Response
    {
        return new Response(sprintf('<!DOCTYPE html><html><head></head><body>Eveltic Crud Controller <strong>%s</strong></body></html>', __FUNCTION__));
    }
}
