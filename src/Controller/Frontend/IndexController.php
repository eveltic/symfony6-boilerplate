<?php

namespace App\Controller\Frontend;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Eveltic\FormUpload\MyLib;

#[Route('/', defaults: [], name: 'app_frontend_index_')]
class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        dump(MyLib::test());
        dump($request);
        return new Response('<!DOCTYPE html><html><head></head><body>Frontend index controller</body></html>');
    }
}
