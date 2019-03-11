<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\Definition\Builder;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class MenuNodeBuilder extends NodeBuilder
{
    public function __construct()
    {
        parent::__construct();

        $this->nodeMapping[MenuNodeDefinition::NAME] = MenuNodeDefinition::class;
    }

    public function menuNode(string $name): NodeDefinition
    {
        return $this->node($name, MenuNodeDefinition::NAME);
    }
}
