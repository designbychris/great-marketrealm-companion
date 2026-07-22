<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Http\Controllers\AppController;
use GreatMarketrealmCompanion\Navigation\Navigation;
use WP_Post;

defined('ABSPATH') || exit;

/**
 * Front-end Service Provider.
 *
 * Connects WordPress to the Marketrealm Companion application.
 * WordPress-specific hooks remain here, while application request
 * handling is delegated to the AppController.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.0
 */
class FrontendServiceProvider extends ServiceProvider
{
    /**
     * Register front-end services.
     */
    public function register(): void
    {
        $this->app->container()->bind(
            AppController::class,
            static function (Container $container): AppController {
                return new AppController(
                    $container->make(Router::class),
                    $container->make(ViewFactory::class),
                    $container->make(Navigation::class)
                );
            }
        );
    }

    /**
     * Register WordPress front-end hooks.
     */
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
     * Load the Companion application assets.
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
     * Render the Companion application shortcode.
     *
     * The attributes and content parameters are retained for compatibility
     * with the WordPress shortcode callback API.
     *
     * @param array<string, mixed> $attributes Shortcode attributes.
     * @param string|null          $content    Enclosed shortcode content.
     */
    public function renderApp(
        array $attributes = [],
        ?string $content = null
    ): string {
        unset($attributes, $content);

        return $this->app
            ->make(AppController::class)
            ->handle();
    }

    /**
     * Determine whether the current page contains the Companion shortcode.
     */
    protected function isCompanionPage(): bool
    {
        if (! is_singular()) {
            return false;
        }

        global $post;

        if (! $post instanceof WP_Post) {
            return false;
        }

        return has_shortcode(
            $post->post_content,
            'gmrc_app'
        );
    }
}
