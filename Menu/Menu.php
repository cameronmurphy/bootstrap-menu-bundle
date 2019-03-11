<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\Menu;

use Symfony\Component\Config\Loader\LoaderInterface;

class Menu
{
    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var mixed
     */
    private $resource;

    /**
     * @var array
     */
    private $definition;

    /**
     * @param LoaderInterface $loader
     * @param mixed           $resource
     */
    public function __construct(LoaderInterface $loader, $resource)
    {
        $this->loader = $loader;
        $this->resource = $resource;
    }

    /**
     * @throws \Exception
     *
     * @return array
     */
    public function getDefinition()
    {
        if (null === $this->definition) {
            $this->definition = $this->loader->load($this->resource, 'yaml');
        }

        return $this->definition;
    }
}
