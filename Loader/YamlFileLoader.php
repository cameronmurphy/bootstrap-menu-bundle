<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\Loader;

use Camurphy\BootstrapMenuBundle\Definition\Builder\MenuTreeBuilder;
use Camurphy\BootstrapMenuBundle\Definition\MenuConfiguration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Yaml\Parser as YamlParser;

class YamlFileLoader extends FileLoader
{
    const ROOT_NAME = 'menu';

    /**
     * @var YamlParser
     */
    private $yamlParser;

    /**
     * @var TreeBuilder
     */
    private $treeBuilder;

    /**
     * @var Processor
     */
    private $processor;

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);

        if (!stream_is_local($path)) {
            throw new \InvalidArgumentException(sprintf('This is not a local file "%s".', $path));
        }

        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $path));
        }

        if (null === $this->yamlParser) {
            $this->yamlParser = new YamlParser();
        }

        $menu = $this->yamlParser->parse(file_get_contents($path));

        // empty file
        if (null === $menu) {
            return [];
        }

        if (null === $this->treeBuilder) {
            $this->treeBuilder = new TreeBuilder();
        }

        $rootNode = $this->treeBuilder->root(self::ROOT_NAME, 'menu', new MenuTreeBuilder());

        // Tree node level added in order to keep the array keys for the first level of nodes
        $rootNode->menuNodeHierarchy()->end();

        if (null === $this->processor) {
            $this->processor = new Processor();
        }

        return $this->processor->processConfiguration(new MenuConfiguration(), $menu);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return \is_string($resource) &&
            \in_array(pathinfo($resource, PATHINFO_EXTENSION), ['yml', 'yaml'], true) &&
            (!$type || 'yaml' === $type);
    }
}
