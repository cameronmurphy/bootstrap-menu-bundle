<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('bootstrap_menu');
        $rootNode = method_exists(TreeBuilder::class, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('swiftmailer');

        $rootNode
            ->beforeNormalization()
                ->ifNull()
                ->thenEmptyArray()
            ->end()
            ->children()
                ->append($this->getMenusNode())
            ->end();

        return $treeBuilder;
    }

    private function getMenusNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('menus');
        $node = method_exists(TreeBuilder::class, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('mailers');

        $node
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('label')->end()
                    ->booleanNode('is_separator')->defaultFalse()->end()
                    ->scalarNode('header_text')->end()
                    ->scalarNode('route')->end()
                    ->arrayNode('route_parameters')
                        ->prototype('variable')
                        ->end()
                    ->end()
                    ->scalarNode('uri')->end()
                    ->booleanNode('display')->defaultTrue()->end()
                    ->integerNode('order')->end()
                    ->arrayNode('attributes')
                        ->prototype('variable')
                        ->end()
                    ->end()
                    ->arrayNode('link_attributes')
                        ->prototype('variable')
                        ->end()
                    ->end()
                    ->arrayNode('children_attributes')
                        ->prototype('variable')
                        ->end()
                    ->end()
                    ->arrayNode('label_attributes')
                        ->prototype('variable')
                        ->end()
                    ->end()
                    ->arrayNode('roles')
                        ->prototype('scalar')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
