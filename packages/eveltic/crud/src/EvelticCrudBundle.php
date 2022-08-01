<?php
namespace Eveltic\Crud;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class EvelticCrudBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // load an XML, PHP or Yaml file
        $container->import('../config/services.yaml');

        // you can also add or replace parameters and services
        // $container->parameters()
        //     ->set('configuration_groups', $config['phrase'])
        // ;

        // if ($config['configuration_groups']) {
        //     $container->services()
        //         ->get('configuration_groups')
        //             ->class(ScreamingPrinter::class)
        //     ;
        // }
    }
    
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}