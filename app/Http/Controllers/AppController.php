<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Http\Controllers;

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Navigation\MenuItem;
use GreatMarketrealmCompanion\Navigation\Navigation;
use GreatMarketrealmCompanion\Core\Http\Response;
use GreatMarketrealmCompanion\Core\Http\Contracts\RouteResolverInterface;
use GreatMarketrealmCompanion\Core\View\Contracts\LayoutRendererInterface;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Application Front Controller.
 *
 * Handles all incoming Companion requests.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.4.0
 */
class AppController
{
    /**
     * Constructor.
     */
    public function __construct(
        protected Router $router,
        protected ViewFactory $views,
        protected Navigation $navigation,
        protected RouteResolverInterface $routes,
        protected LayoutRendererInterface $layout,
    ) {
    }

    /**
     * Handle the application request.
     */
    public function handle(): string|Response
    {    
        $route = $this->routes->current();

        error_log(
            'AppController route: ' . $route
        );
    
        error_log(
            'AppController Router ID: '
            . spl_object_id($this->router)
        );
    
        error_log(
            'AppController has GET /characters: '
            . (
                $this->router->has(
                    'GET',
                    '/characters'
                )
                    ? 'yes'
                    : 'no'
            )
        );
    
        try {
            $result = $this->router->dispatch(
                null,
                '/' . $route
            );
        
            if ($result instanceof Response) {
                return $result;
            }
        
            if (! is_string($result)) {
                throw new RuntimeException(
                    sprintf(
                        'Expected route "%s" to return string or Response; received %s.',
                        $route,
                        get_debug_type($result)
                    )
                );
            }
        
            $content = $result;
            $pageTitle = $this->pageTitle($route);
        } catch (\Throwable $exception) {
            error_log(
                sprintf(
                    'AppController Throwable: %s: %s in %s:%d',
                    get_class($exception),
                    $exception->getMessage(),
                    $exception->getFile(),
                    $exception->getLine()
                )
            );
        
            error_log(
                $exception->getTraceAsString()
            );
        
            $content = $this->views->render(
                View::make(
                    'dashboard.not-found',
                    [
                        'requestedRoute' => $route,
                    ]
                )
            );
        
            $pageTitle = __(
                'Not Found',
                'great-marketrealm-companion'
            );
        }
    
        return $this->layout->render(
            [
                'pageTitle'    => $pageTitle,
                'content'      => $content,
                'currentRoute' => $route,
                'navigation'   => $this->navigationItems($route),
            ]
        );
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

                    'active' => $route === $currentRoute
                    || str_starts_with(
                        $currentRoute,
                        $route . '/'
                    ),

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
}
