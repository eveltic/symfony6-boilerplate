<?php

namespace App\Controller\Frontend;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', defaults: [], name: 'app_frontend_index_')]
class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        return new Response('<!DOCTYPE html><html><head></head><body>Frontend index controller</body></html>');
    }
}
