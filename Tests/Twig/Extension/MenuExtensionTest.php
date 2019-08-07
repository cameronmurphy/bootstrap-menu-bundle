<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\Tests\Twig\Extension;

use Camurphy\BootstrapMenuBundle\Twig\Extension\MenuExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\Extension\SecurityExtension;
use Twig\Environment as TwigEnvironment;

/**
 * @internal
 * @coversNothing
 */
class MenuExtensionTest extends TestCase
{
    private static $mockMenus = [
        'main' => [
            'items' => [
                'test_drop_down' => [
                    'label' => 'Test Drop Down',
                    'items' => [
                        'first_drop_down_item' => [
                            'label' => 'First Drop Down Item',
                            'route' => 'app_first_drop_down_link',
                            'roles' => ['ROLE_ADMINISTRATOR'],
                        ],
                    ],
                ],
            ],
        ],
    ];

    /**
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function testRenderMenu(): void
    {
        /** @var TwigEnvironment $mockTwigEnvironment */
        $mockTwigEnvironment = $this->mockTwigEnvironment();

        $menuExtension = new MenuExtension(self::$mockMenus);
        $menu = $menuExtension->renderMenu($mockTwigEnvironment, 'main');
    }

    private function mockTwigEnvironment(): MockObject
    {
        $twigMock = $this->getMockBuilder(TwigEnvironment::class)
            ->disableOriginalConstructor()
            ->getMock();

        $securityExtensionMock = $this->getMockBuilder(SecurityExtension::class)
            ->disableOriginalConstructor()
            ->getMock();

        $securityExtensionMock
            ->method('isGranted')
            ->willReturnCallback(function ($expression) {
                return 'ROLE_USER' === $expression;
            });

        $twigMock
            ->expects($this->any())
            ->method('getExtension')
            ->willReturn($securityExtensionMock);

        return $twigMock;
    }
}
