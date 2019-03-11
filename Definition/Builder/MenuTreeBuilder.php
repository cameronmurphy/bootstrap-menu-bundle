<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\Definition\Builder;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;

class MenuTreeBuilder extends NodeBuilder
{
    public function __construct()
    {
        parent::__construct();

        $this->nodeMapping['menu'] = __NAMESPACE__ . '\\MenuNodeDefinition';
    }

    public function menuNode(string $name): NodeDefinition
    {
        return $this->node($name, 'menu');
    }
}
