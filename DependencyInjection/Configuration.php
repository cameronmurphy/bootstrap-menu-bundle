<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bootstrap_menu');

        $rootNode
            ->children()
            ->arrayNode('menu')
            ->isRequired()
            ->children()
            ->scalarNode('resource')
            ->isRequired()
            ->cannotBeEmpty()
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
