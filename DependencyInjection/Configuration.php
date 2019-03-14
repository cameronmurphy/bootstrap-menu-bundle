<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('bootstrap_menu');
        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $this->addMenusNode($rootNode);

        return $treeBuilder;
    }

    private function addMenusNode(ArrayNodeDefinition $rootNode): void
    {
        $menuItemNodeBuilder = $rootNode
            ->fixXmlConfig('menu')
            ->children()
                ->arrayNode('menus')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->disallowNewKeysInSubsequentConfigs()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('items')
                                ->prototype('array')
                                    ->children();

        $this->configureItemsNode($menuItemNodeBuilder);
    }

    private function configureItemsNode(NodeBuilder $builder, int $depth = 1): void
    {
        $builder
            ->scalarNode('label')->end()
            ->booleanNode('is_divider')->defaultFalse()->end()
            ->scalarNode('route')->end()
            ->arrayNode('route_parameters')
                ->prototype('variable')
                ->end()
            ->end()
            ->scalarNode('url')->end()
            ->arrayNode('roles')
                ->prototype('scalar')
                ->end()
            ->end();

        if ($depth > 0) {
            $itemsNodeBuilder = $builder
                ->arrayNode('items')
                    ->requiresAtLeastOneElement()
                    ->prototype('array')
                        ->children();

            $this->configureItemsNode($itemsNodeBuilder, $depth - 1);

            $itemsNodeBuilder->end();
        }
    }
}
