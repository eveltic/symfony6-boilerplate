<?php

namespace App\Controller\Backend;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/', defaults: [], name: 'app_backend_index_')]
class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        return $this->render('app/backend/index/index.html.twig', []);
        //return new Response('<!DOCTYPE html><html><head></head><body>Backend index controller</body></html>');
    }
}
