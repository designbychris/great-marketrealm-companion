<?php

namespace GreatMarketrealmCompanion\Http\Controllers;

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Navigation\MenuItem;
use GreatMarketrealmCompanion\Navigation\Navigation;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Application Front Controller.
 *
 * Handles all incoming Companion requests.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class AppController
{
    /**
     * Router.
     */
    protected Router $router;

    /**
     * View factory.
     */
    protected ViewFactory $views;

    /**
     * Navigation.
     */
    protected Navigation $navigation;

    /**
     * Constructor.
     */
    public function __construct(
        Router $router,
        ViewFactory $views,
        Navigation $navigation
    ) {
        $this->router = $router;
        $this->views = $views;
        $this->navigation = $navigation;
    }

    /**
     * Handle the application request.
     */
    public function handle(): string
    {
        $route = $this->requestedRoute();

        try {
            $content = $this->router->dispatch(
                'GET',
                '/' . $route
            );

            $pageTitle = $this->pageTitle($route);
        } catch (RuntimeException) {

            $content = $this->views->render(
                View::make(
                    'dashboard.not-found',
                    [
                        'requestedRoute' => $route,
                    ]
                )
            );

            $pageTitle = __('Not Found', 'great-marketrealm-companion');
        }

        return $this->renderLayout(
            [
                'pageTitle'    => $pageTitle,
                'content'      => $content,
                'currentRoute' => $route,
                'navigation'   => $this->navigationItems($route),
            ]
        );
    }

    /**
     * Determine requested route.
     */
    protected function requestedRoute(): string
    {
        $route = isset($_GET['gmrc_route'])
            ? sanitize_key(wp_unslash($_GET['gmrc_route']))
            : 'dashboard';

        return $route !== ''
            ? $route
            : 'dashboard';
    }

    /**
     * Build navigation.
     *
     * @return array<int,array<string,mixed>>
     */
    protected function navigationItems(
        string $currentRoute
    ): array {

        $items = array_values(
            $this->navigation->items()
        );

        usort(
            $items,
            static fn (
                MenuItem $a,
                MenuItem $b
            ) => $a->sortOrder() <=> $b->sortOrder()
        );

        return array_map(
            function (MenuItem $item) use ($currentRoute): array {

                $route = $item->route();

                return [

                    'key' => $item->key(),

                    'label' => $item->label(),

                    'icon' => $item->icon(),

                    'route' => $route,

                    'url' => $this->routeUrl($route),

                    'enabled' => $this->router->has(
                        'GET',
                        '/' . $route
                    ),

                    'active' => $route === $currentRoute,

                ];

            },
            $items
        );
    }

    /**
     * Build application URL.
     */
    protected function routeUrl(
        string $route
    ): string {

        $base = get_permalink();

        if (! is_string($base)) {
            $base = home_url('/companion/');
        }

        if ($route === 'dashboard') {
            return remove_query_arg(
                'gmrc_route',
                $base
            );
        }

        return add_query_arg(
            'gmrc_route',
            $route,
            $base
        );
    }

    /**
     * Resolve page title.
     */
    protected function pageTitle(
        string $route
    ): string {

        foreach (
            $this->navigation->items() as $item
        ) {
            if ($item->route() === $route) {
                return $item->label();
            }
        }

        return ucfirst($route);
    }

    /**
     * Render application layout.
     *
     * @param array<string,mixed> $data
     */
    protected function renderLayout(
        array $data
    ): string {

        $layout = GMRC_PATH .
            'app/Core/View/Templates/layouts/app.php';

        if (! file_exists($layout)) {
            return '<p>Layout not found.</p>';
        }

        extract(
            $data,
            EXTR_SKIP
        );

        ob_start();

        require $layout;

        return (string) ob_get_clean();
    }
}
