<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\Twig\Extension;

use Symfony\Bridge\Twig\Extension\SecurityExtension;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    /**
     * @var string[]
     */
    private $menus;

    public function __construct(array $menus)
    {
        $this->menus = $menus;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_bootstrap_menu', [$this, 'renderMenu'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @param TwigEnvironment $environment
     * @param string          $menuName
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     *
     * @return string
     */
    public function renderMenu(TwigEnvironment $environment, string $menuName): string
    {
        $html = '';

        if (!\array_key_exists($menuName, $this->menus)) {
            throw new \RuntimeException(sprintf('Menu %s is not configured', $menuName));
        }

        $menuDefinition = $this->menus[$menuName];
        /** @var SecurityExtension $securityExtension */
        $securityExtension = $environment->getExtension(SecurityExtension::class);

        // Strip out items that shouldn't be displayed
        foreach ($menuDefinition['items'] as $index => &$menuItem) {
            if ($this->recursivePrune($menuItem, $securityExtension)) {
                unset($menuDefinition['items'][$index]);
            }
        }

        unset($menuItem);

        foreach ($menuDefinition['items'] as $menuItem) {
            if (\array_key_exists('items', $menuItem) && \count($menuItem['items']) > 0) {
                $html .= $environment->render('@BootstrapMenu/dropDown.html.twig', $menuItem);
            } else {
                $html .= $environment->render('@BootstrapMenu/link.html.twig', $menuItem);
            }
        }

        return $html;
    }

    /**
     * @param array             $menuItem
     * @param SecurityExtension $securityExtension
     *
     * @return bool Whether the current item should be pruned from the menu
     */
    private function recursivePrune(array &$menuItem, SecurityExtension $securityExtension): bool
    {
        if (\count($menuItem['roles']) > 0) {
            $granted = false;

            foreach ($menuItem['roles'] as $role) {
                if ($securityExtension->isGranted($role)) {
                    $granted = true;
                    break;
                }
            }

            if (!$granted) {
                return true;
            }
        }

        if (\array_key_exists('items', $menuItem) && \count($menuItem['items']) > 0) {
            $itemCount = 0;

            $currentSeparator = null;
            $separatorsInUse = [];

            foreach ($menuItem['items'] as $subItemKey => &$subMenuItem) {
                if ($this->recursivePrune($subMenuItem, $securityExtension)) {
                    unset($menuItem['items'][$subItemKey]);
                } elseif ($subMenuItem['is_divider']) {
                    $currentSeparator = $subItemKey;
                } else {
                    ++$itemCount;

                    if (!\in_array($currentSeparator, $separatorsInUse, true)) {
                        $separatorsInUse[] = $currentSeparator;
                    }
                }
            }

            unset($subMenuItem);

            // Prune unused separators
            foreach ($menuItem['items'] as $subItemKey => $subMenuItem) {
                if ($subMenuItem['is_divider'] && !\in_array($subItemKey, $separatorsInUse, true)) {
                    unset($menuItem['items'][$subItemKey]);
                }
            }

            if (0 == $itemCount) {
                return true;
            }
        }

        return false;
    }
}
