<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\Definition\Builder;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class MenuNodeDefinition extends ArrayNodeDefinition
{
    const NAME = 'menu';

    public function menuNode(int $depth = 10): self
    {
        if (0 == $depth) {
            return $this;
        }

        return $this
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
            ->arrayNode('modules')
            ->prototype('scalar')
            ->end()
            ->end()
            ->menuNode('children')->menuNodeHierarchy($depth - 1)
            ->end()
            ->end();
    }
}
