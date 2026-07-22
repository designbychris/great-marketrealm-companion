<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Core\View\View;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Navigation\MenuItem;
use GreatMarketrealmCompanion\Navigation\Navigation;
use RuntimeException;

defined('ABSPATH') || exit;

/**
 * Front-end Service Provider.
 *
 * Connects WordPress to the Marketrealm Companion application.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class FrontendServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // No additional bindings are required.
    }

    public function boot(): void
    {
        add_action(
            'wp_enqueue_scripts',
            [$this, 'enqueueAssets']
        );

        add_shortcode(
            'gmrc_app',
            [$this, 'renderApp']
        );
    }

    /**
     * Load Companion assets.
     */
    public function enqueueAssets(): void
    {
        if (! $this->isCompanionPage()) {
            return;
        }

        wp_enqueue_style(
            'gmrc-companion-app',
            GMRC_URL . 'assets/css/companion-app.css',
            [],
            GMRC_VERSION
        );
    }

    /**
     * Render the application.
     */
    public function renderApp(): string
    {
        $route = $this->requestedRoute();
        $router = $this->app->make(Router::class);

        try {
            $content = $router->dispatch(
                'GET',
                '/' . $route
            );

            $pageTitle = $this->pageTitle($route);
        } catch (RuntimeException $exception) {
            $content = $this->renderNotFound($route);
            $pageTitle = __('Not Found', 'great-marketrealm-companion');
        }

        return $this->renderLayout(
            [
                'pageTitle'   => $pageTitle,
                'content'     => $content,
                'currentRoute' => $route,
                'navigation'  => $this->navigationItems(
                    $router,
                    $route
                ),
            ]
        );
    }

    /**
     * Return the requested application route.
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
     * Build layout-ready navigation data.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function navigationItems(
        Router $router,
        string $currentRoute
    ): array {
        $navigation = $this->app->make(
            Navigation::class
        );

        $items = array_values(
            $navigation->items()
        );

        usort(
            $items,
            static fn (MenuItem $first, MenuItem $second): int =>
                $first->sortOrder() <=> $second->sortOrder()
        );

        return array_map(
            function (MenuItem $item) use (
                $router,
                $currentRoute
            ): array {
                $route = $item->route();
                $enabled = $router->has(
                    'GET',
                    '/' . $route
                );

                return [
                    'key'     => $item->key(),
                    'label'   => $item->label(),
                    'icon'    => $item->icon(),
                    'route'   => $route,
                    'url'     => $this->routeUrl($route),
                    'enabled' => $enabled,
                    'active'  => $route === $currentRoute,
                ];
            },
            $items
        );
    }

    /**
     * Create an application route URL.
     */
    protected function routeUrl(string $route): string
    {
        $baseUrl = get_permalink();

        if (! is_string($baseUrl)) {
            $baseUrl = home_url('/companion/');
        }

        if ($route === 'dashboard') {
            return remove_query_arg(
                'gmrc_route',
                $baseUrl
            );
        }

        return add_query_arg(
            'gmrc_route',
            $route,
            $baseUrl
        );
    }

    /**
     * Resolve a page title from its navigation item.
     */
    protected function pageTitle(string $route): string
    {
        $navigation = $this->app->make(
            Navigation::class
        );

        foreach ($navigation->items() as $item) {
            if ($item->route() === $route) {
                return $item->label();
            }
        }

        return ucfirst($route);
    }

    /**
     * Render the Companion not-found screen.
     */
    protected function renderNotFound(
        string $route
    ): string {
        $views = $this->app->make(
            ViewFactory::class
        );

        return $views->render(
            View::make(
                'dashboard.not-found',
                [
                    'requestedRoute' => $route,
                ]
            )
        );
    }

    /**
     * Render the shared application layout.
     *
     * @param array<string, mixed> $data Layout variables.
     */
    protected function renderLayout(
        array $data
    ): string {
        $layout = GMRC_PATH
            . 'app/Views/layouts/app.php';

        if (! file_exists($layout)) {
            return '<p>Marketrealm Companion layout could not be loaded.</p>';
        }

        extract($data, EXTR_SKIP);

        ob_start();

        require $layout;

        return (string) ob_get_clean();
    }

    /**
     * Determine whether the current page contains the Companion.
     */
    protected function isCompanionPage(): bool
    {
        if (! is_singular()) {
            return false;
        }

        global $post;

        return $post instanceof \WP_Post
            && has_shortcode(
                $post->post_content,
                'gmrc_app'
            );
    }
}
