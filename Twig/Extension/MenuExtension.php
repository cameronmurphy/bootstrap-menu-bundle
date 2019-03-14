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
     * @param string $menuName
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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

        if (\count($menuItem['items']) > 0) {
            $childCount = 0;

            $currentSeparator = null;
            $separatorsInUse = [];

            foreach ($menuItem['items'] as $childKey => &$childMenuItem) {
                if ($this->recursivePrune($childMenuItem, $securityExtension)) {
                    unset($menuItem['items'][$childKey]);
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
            foreach ($menuItem['items'] as $childKey => $childMenuItem) {
                if ($childMenuItem['is_separator'] && !\in_array($childKey, $separatorsInUse, true)) {
                    unset($menuItem['items'][$childKey]);
                }
            }

            if (0 == $childCount) {
                return true;
            }
        }

        return false;
    }
}
