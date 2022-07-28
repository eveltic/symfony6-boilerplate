<?php

namespace App\Controller\Backend;


use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;
use Eveltic\Crud\CrudFactory;
use Eveltic\Crud\Field\StringField;
use Eveltic\Crud\Field\ArrayField;
use Eveltic\Crud\Configuration\Type\FieldType;
use Eveltic\Crud\Configuration\Group\FieldGroup;
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

        return new CrudFactory($queryBuilder, $fields);
    }
}
