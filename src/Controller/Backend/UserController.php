<?php

namespace App\Controller\Backend;


use Doctrine\Persistence\ManagerRegistry;
use Eveltic\Crud\Configuration\Group\AccessGroup;
use Symfony\Component\Routing\Annotation\Route;
use Eveltic\Crud\CrudFactory;
use Eveltic\Crud\Field\StringField;
use Eveltic\Crud\Field\ArrayField;
use Eveltic\Crud\Configuration\Type\FieldType;
use Eveltic\Crud\Configuration\Group\FieldGroup;
use Eveltic\Crud\Configuration\Type\AccessType;
use Eveltic\Crud\Controller\AbstractCrudController;

#[Route('/user', defaults: [], name: 'app_backend_user_')]
class UserController extends AbstractCrudController
{
    protected function configureCrud(ManagerRegistry $doctrine)
    {
        $queryBuilder = $doctrine->getManager()->createQueryBuilder()->from(User::class, 'user')->select('user.email, user.roles');


        $fields = new FieldGroup(
            new FieldType('user.email', StringField::class, 'Email', true, true, true, []),
            new FieldType('user.roles', ArrayField::class, 'Roles', false, false, true, []),
        );


        $accesses = new AccessGroup(
            new AccessType('index', true, ['ROLE_ADMIN'] ),
            new AccessType('create', true, ['ROLE_ADMIN']),
            new AccessType('edit', true, ['ROLE_ADMIN']),
            new AccessType('remove', true, ['ROLE_ADMIN']),
            new AccessType('export', true, ['ROLE_ADMIN']),
            new AccessType('paginate', true, ['ROLE_ADMIN']),
            new AccessType('search', true, ['ROLE_ADMIN']),
            new AccessType('order', true, ['ROLE_ADMIN'])
        );

        return new CrudFactory($queryBuilder, $fields, $accesses);
    }
}
