<?php

namespace App\Table;


use App\Entity\User; // your entity class ...
use HelloSebastian\HelloBootstrapTableBundle\Columns\ColumnBuilder;
use HelloSebastian\HelloBootstrapTableBundle\Columns\TextColumn;
use HelloSebastian\HelloBootstrapTableBundle\Columns\DateTimeColumn;
use HelloSebastian\HelloBootstrapTableBundle\Columns\HiddenColumn;
use HelloSebastian\HelloBootstrapTableBundle\Columns\ActionColumn;
use HelloSebastian\HelloBootstrapTableBundle\Columns\BooleanColumn;
use HelloSebastian\HelloBootstrapTableBundle\HelloBootstrapTable;

class UserTable extends HelloBootstrapTable
{
  
    protected function buildColumns(ColumnBuilder $builder, $options)
    {
        $builder
            ->add("id", HiddenColumn::class)
            ->add('email', TextColumn::class, array(
                'title' => 'E-Mail',
                'visible' => true
            ))
            ->add("actions", ActionColumn::class, array(
                'title' => 'Actions',
                'width' => 150,
                'buttons' => array( //see ActionButton for more examples.
                    array(
                        'displayName' => 'open',
                        'routeName' => 'app_frontend_index_index',
                        'classNames' => 'btn btn-xs',
                        'additionalClassNames' => 'btn-success mr-1'
                    ),
                    array(
                        'displayName' => 'edit',
                        'routeName' => 'app_frontend_index_index',
                        'classNames' => 'btn btn-xs btn-warning',
                        'addIf' => function(User $user) {
                            // In this callback it is decided if the button will be rendered.
                            return $this->security->isGranted('ROLE_ADMIN');
                        }
                    )
                )
            ));
    }

    protected function getEntityClass()
    {
        return User::class;
    }
}
