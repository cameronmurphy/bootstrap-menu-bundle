<?php

declare(strict_types=1);

namespace Camurphy\BootstrapMenuBundle\Tests\Twig\Extension;

use Camurphy\BootstrapMenuBundle\Twig\Extension\MenuExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Bridge\Twig\Extension\SecurityExtension;
use Twig\Environment as TwigEnvironment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

/**
 * @internal
 * @coversNothing
 */
class MenuExtensionTest extends TestCase
{
    use MatchesSnapshots;

    private $rootPath;

    private static $mockMenus = [
        'main' => [
            'items' => [
                'test_drop_down_1' => [
                    'label' => 'Test Drop Down 1',
                    'items' => [
                        'drop_down_item_1' => [
                            'label' => 'Drop Down Item 1 (this should be pruned)',
                            'route' => 'app_drop_down_1_route',
                            'roles' => ['ROLE_ADMINISTRATOR'],
                            'is_divider' => false,
                        ],
                        'drop_down_item_2' => [
                            'label' => 'Drop Down Item 2 (this should render a drop-down-item)',
                            'route' => 'app_drop_down_2_route',
                            'roles' => [],
                            'route_parameters' => ['test' => '123'],
                            'is_divider' => false,
                        ],
                    ],
                    'roles' => [],
                ],
                'test_drop_down_2' => [
                    'label' => 'Test Drop Down 2',
                    'items' => [
                        'drop_down_item_3' => [
                            'label' => 'Drop Down Item 3 (this should render a drop-down-item)',
                            'route' => 'app_drop_down_item_3_route',
                            'roles' => ['ROLE_USER'],
                            'route_parameters' => [],
                            'is_divider' => false,
                        ],
                        'drop_down_item_4' => [
                            'label' => 'Drop Down Item 4 (should render a drop-down-item linking to Disney)',
                            'url' => 'https://disney.com',
                            'roles' => [],
                            'is_divider' => false,
                        ],
                        'divider_1' => [
                            'label' => 'Divider 1 (this should be pruned, there\'s nothing below it',
                            'roles' => [],
                            'is_divider' => true,
                        ],
                    ],
                    'roles' => [],
                ],
                'test_drop_down_3' => [
                    'label' => 'Test Drop Down 3 (this whole menu should be pruned due to permissions)',
                    'items' => [
                        'drop_down_item_5' => [
                            'label' => 'Drop Down Item 5',
                            'roles' => ['ROLE_ADMINISTRATOR'],
                            'is_divider' => false,
                        ],
                        'drop_down_item_6' => [
                            'label' => 'Drop Down Item 6',
                            'roles' => ['ROLE_SUPER_USER'],
                            'is_divider' => false,
                        ],
                        'drop_down_item_7' => [
                            'label' => 'Drop Down Item 7',
                            'roles' => ['ROLE_ADMINISTRATOR'],
                            'is_divider' => false,
                        ],
                    ],
                    'roles' => [],
                ],
                'test_link_1' => [
                    'label' => 'Link 1 (this should render a link)',
                    'route' => 'app_link_1_route',
                    'route_parameters' => [],
                    'roles' => [],
                ],
            ],
        ],
    ];

    public function setUp(): void
    {
        $this->rootPath = realpath(__DIR__ . '/../../../');
    }

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

        $this->assertMatchesSnapshot($menu);
    }

    /**
     * @throws \Twig\Error\LoaderError
     *
     * @return MockObject
     */
    private function mockTwigEnvironment(): MockObject
    {
        $loader = new FilesystemLoader([], $this->rootPath);
        $loader->addPath('Resources/views', 'BootstrapMenu');

        /** @var \Twig\Environment|MockObject $twigMock */
        $twigMock = $this->getMockBuilder(TwigEnvironment::class)
            ->setConstructorArgs([$loader])
            ->setMethods(['getExtension'])
            ->getMock();

        $securityExtensionMock = $this->getMockBuilder(SecurityExtension::class)
            ->disableOriginalConstructor()
            ->setMethods(['isGranted', 'getDefaultStrategy'])
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

        $pathFunction = new TwigFunction('path', function ($route, $routeParameters = []): string {
            $path = '/' . str_replace('_', '-', $route);

            if (\count($routeParameters) > 0) {
                $path .= '?' . http_build_query($routeParameters);
            }

            return $path;
        });

        $twigMock->addFunction($pathFunction);

        return $twigMock;
    }
}
