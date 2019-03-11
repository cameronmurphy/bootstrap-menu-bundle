<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\Definition;

use Camurphy\BootstrapMenuBundle\Definition\Builder\MenuNodeBuilder;
use Camurphy\BootstrapMenuBundle\Definition\Builder\MenuNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class MenuConfiguration implements ConfigurationInterface
{
    const ROOT_NODE = 'menu';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root(self::ROOT_NODE, MenuNodeDefinition::NAME, new MenuNodeBuilder())->end();

        return $treeBuilder;
    }
}
