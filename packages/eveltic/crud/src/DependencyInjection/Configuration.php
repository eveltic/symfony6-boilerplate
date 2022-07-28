<?php

namespace Eveltic\Crud\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {

        $treeBuilder = new TreeBuilder('eveltic_crud');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('configuration_groups')
                ->end() // twitter
            ->end()
        ;

        return $treeBuilder;
    }
}
