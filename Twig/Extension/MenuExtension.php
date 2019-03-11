<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\Twig\Extension;

use Camurphy\BootstrapMenuBundle\Menu\Menu;
use Symfony\Bridge\Twig\Extension\SecurityExtension;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    /**
     * @var Menu
     */
    private $menu;

    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_menu', [$this, 'renderMenu'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @param TwigEnvironment $environment
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     * @throws \Exception
     *
     * @return string
     */
    public function renderMenu(TwigEnvironment $environment)
    {
        $html = '';

        /** @var SecurityExtension $securityExtension */
        $securityExtension = $environment->getExtension(SecurityExtension::class);
        $menuDefinition = $this->menu->getDefinition();

        // Strip out items that shouldn't be displayed
        foreach ($menuDefinition as $index => &$menuItem) {
            if ($this->recursivePrune($menuItem, $securityExtension)) {
                unset($menuDefinition[$index]);
            }
        }

        unset($menuItem);

        foreach ($menuDefinition as $menuItem) {
            if (\count($menuItem['children']) > 0) {
                $html .= $environment->render('@CamurphyBootstrapMenu/dropDown.html.twig', $menuItem);
            } else {
                $html .= $environment->render('@CamurphyBootstrapMenu/link.html.twig', $menuItem);
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
    private function recursivePrune(array &$menuItem, SecurityExtension $securityExtension)
    {
        if (!$menuItem['display']) {
            return true;
        }

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

        if (\count($menuItem['children']) > 0) {
            $childCount = 0;

            $currentSeparator = null;
            $separatorsInUse = [];

            foreach ($menuItem['children'] as $childKey => &$childMenuItem) {
                if ($this->recursivePrune($childMenuItem, $securityExtension)) {
                    unset($menuItem['children'][$childKey]);
                } elseif ($childMenuItem['is_separator']) {
                    $currentSeparator = $childKey;
                } else {
                    ++$childCount;

                    if (!\in_array($currentSeparator, $separatorsInUse, true)) {
                        $separatorsInUse[] = $currentSeparator;
                    }
                }
            }

            unset($childMenuItem);

            // Prune unused separators
            foreach ($menuItem['children'] as $childKey => $childMenuItem) {
                if ($childMenuItem['is_separator'] && !\in_array($childKey, $separatorsInUse, true)) {
                    unset($menuItem['children'][$childKey]);
                }
            }

            if (0 == $childCount) {
                return true;
            }
        }

        return false;
    }
}
