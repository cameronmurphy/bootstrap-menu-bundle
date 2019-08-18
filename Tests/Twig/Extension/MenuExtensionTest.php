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
                'dropdown_menu_1' => [
                    'label' => 'Dropdown Menu 1',
                    'items' => [
                        'dropdown_item_1' => [
                            'label' => 'Dropdown Item 1 (should be pruned due to permissions)',
                            'route' => 'app_dropdown_1_route',
                            'roles' => ['ROLE_ADMINISTRATOR'],
                            'is_divider' => false,
                        ],
                        'dropdown_item_2' => [
                            'label' => 'Dropdown Item 2 (should render a dropdown-item)',
                            'route' => 'app_dropdown_2_route',
                            'roles' => [],
                            'route_parameters' => ['test' => '123'],
                            'is_divider' => false,
                        ],
                    ],
                    'roles' => [],
                ],
                'dropdown_menu_2' => [
                    'label' => 'Dropdown Menu 2',
                    'items' => [
                        'dropdown_item_3' => [
                            'label' => 'Dropdown Item 3 (should render a dropdown-item because user has permission)',
                            'route' => 'app_dropdown_item_3_route',
                            'roles' => ['ROLE_USER'],
                            'route_parameters' => [],
                            'is_divider' => false,
                        ],
                        'dropdown_item_4' => [
                            'label' => 'Dropdown Item 4 (should render a dropdown-item linking to Disney)',
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
                'dropdown_menu_3' => [
                    'label' => 'Dropdown Menu 3 (this whole menu should be pruned due to permissions)',
                    'items' => [
                        'dropdown_item_5' => [
                            'label' => 'Dropdown Item 5',
                            'route' => 'app_dropdown_item_5_route',
                            'roles' => ['ROLE_ADMINISTRATOR'],
                            'is_divider' => false,
                        ],
                        'dropdown_item_6' => [
                            'label' => 'Dropdown Item 6',
                            'route' => 'app_dropdown_item_6_route',
                            'roles' => ['ROLE_SUPER_USER'],
                            'is_divider' => false,
                        ],
                        'dropdown_item_7' => [
                            'label' => 'Dropdown Item 7',
                            'route' => 'app_dropdown_item_7_route',
                            'roles' => ['ROLE_ADMINISTRATOR'],
                            'is_divider' => false,
                        ],
                    ],
                    'roles' => [],
                ],
                'link_1' => [
                    'label' => 'Link 1 (this should render a nav-link)',
                    'route' => 'app_link_1_route',
                    'route_parameters' => [],
                    'roles' => [],
                ],
                'dropdown_menu_4' => [
                    'label' => 'Dropdown Menu 4',
                    'items' => [
                        'dropdown_item_8' => [
                            'label' => 'Dropdown Item 8',
                            'route' => 'app_dropdown_item_8_route',
                            'route_parameters' => [],
                            'roles' => [],
                            'is_divider' => false,
                        ],
                        'divider_2' => [
                            'label' => 'Divider 2 (should render a dropdown-header)',
                            'roles' => [],
                            'is_divider' => true,
                        ],
                        'dropdown_item_9' => [
                            'label' => 'Dropdown Item 9',
                            'route' => 'app_dropdown_item_9_route',
                            'route_parameters' => [],
                            'roles' => [],
                            'is_divider' => false,
                        ],
                        'divider_3' => [
                            // Should render a dropdown-divider
                            'roles' => [],
                            'is_divider' => true,
                        ],
                        'dropdown_item_10' => [
                            'label' => 'Dropdown Item 10',
                            'route' => 'app_dropdown_item_10_route',
                            'route_parameters' => [],
                            'roles' => [],
                            'is_divider' => false,
                        ],
                        'dropdown_item_11' => [
                            'label' => 'Dropdown Item 11',
                            'route' => 'app_dropdown_item_11_route',
                            'route_parameters' => [],
                            'roles' => [],
                            'is_divider' => false,
                        ],
                        'divider_4' => [
                            'label' => 'Divider 4 (should be pruned because Dropdown Item 12 will also pruned due to permissions)',
                            'roles' => [],
                            'is_divider' => true,
                        ],
                        'dropdown_item_12' => [
                            'label' => 'Dropdown Item 12 (should be pruned due to permissions)',
                            'route' => 'app_dropdown_item_12_route',
                            'route_parameters' => [],
                            'roles' => ['ROLE_ADMINISTRATOR'],
                            'is_divider' => false,
                        ],
                        'divider_5' => [
                            'label' => 'Divider 5',
                            'roles' => [],
                            'is_divider' => true,
                        ],
                        'dropdown_item_13' => [
                            'label' => 'Dropdown Item 13',
                            'route' => 'app_dropdown_item_13_route',
                            'route_parameters' => [],
                            'roles' => [],
                            'is_divider' => false,
                        ],
                    ],
                    'roles' => [],
                ],
                'dropdown_menu_5' => [
                    'label' => 'Dropdown Menu 5 (should be pruned due to permissions)',
                    'items' => [
                        'dropdown_item_14' => [
                            'label' => 'Dropdown Item 14',
                            'route' => 'app_dropdown_item_14_route',
                            'route_parameters' => [],
                            'roles' => [],
                            'is_divider' => false,
                        ],
                    ],
                    'roles' => ['ROLE_ADMINISTRATOR'],
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
