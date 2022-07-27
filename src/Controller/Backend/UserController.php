<?php

namespace App\Controller\Backend;

use Doctrine\Persistence\ManagerRegistry;
use Eveltic\Crud\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Eveltic\Crud\CrudConfiguration;

#[Route('/user', defaults: [], name: 'app_backend_user_')]
class UserController extends AbstractCrudController
{
    protected function configureCrud(ManagerRegistry $doctrine)
    {
        $queryBuilder = $doctrine->getManager()->createQueryBuilder()
            ->from(User::class, 'user')
            ->select('user.email, user.roles');

        return new CrudConfiguration($queryBuilder);
    }
}
