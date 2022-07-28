<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Eveltic\Crud\Configuration\Group\AccessGroup;
use Eveltic\Crud\Configuration\Group\ButtonGroup;
use Symfony\Component\Routing\Annotation\Route;
use Eveltic\Crud\CrudFactory;
use Eveltic\Crud\Field\StringField;
use Eveltic\Crud\Field\ArrayField;
use Eveltic\Crud\Configuration\Type\FieldType;
use Eveltic\Crud\Configuration\Group\FieldGroup;
use Eveltic\Crud\Configuration\Group\FormGroup;
use Eveltic\Crud\Configuration\Group\TextGroup;
use Eveltic\Crud\Configuration\Type\AccessType;
use Eveltic\Crud\Configuration\Type\ButtonType;
use Eveltic\Crud\Configuration\Type\FormType;
use Eveltic\Crud\Configuration\Type\TextType;
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

        $texts = new TextGroup(
            new TextType('title', 'all', 'Users', true),
            new TextType('h1', 'index', 'Users', true),
            new TextType('h2', 'index', 'Management', true),
            new TextType('h1', 'create', 'Users', true),
            new TextType('h2', 'create', 'Create', true),
            new TextType('h1', 'edit', 'Users', true),
            new TextType('h2', 'edit', 'Edit', true)/*,
            new TextType('h1', 'clone', 'Vehicle', true),
            new TextType('h2', 'clone', 'Clone', true)*/
        );

        $forms = new FormGroup(
            new FormType('create', UserType::class, User::class),
            new FormType('edit', UserType::class, User::class),
        );

        $buttons = new ButtonGroup(
            new ButtonType('impersonating', 'Impersonate', 'fas fa-user-secret', ['name' => 'app_backend_user_index', 'params' => ['_switch_user' => 'user.username']], null, ['ROLE_ROOT'], ['modal' => false]),
            new ButtonType('google', 'Google', 'fas fa-user', 'https://eveltic.es', null, ['ROLE_ROOT'], ['modal' => true]),
            new ButtonType('other', 'Other Test', 'fas fa-user', ['name' => 'app_frontend_index_index', 'params' => []], null, ['ROLE_ROOT'], ['modal' => true]),
        );

        return new CrudFactory($queryBuilder, $fields, $accesses, $texts, $forms, $buttons);
    }
}
