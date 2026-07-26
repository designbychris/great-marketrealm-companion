<?php

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Core\Routing\Router;
use GreatMarketrealmCompanion\Core\View\ViewFactory;
use GreatMarketrealmCompanion\Http\Controllers\AppController;
use GreatMarketrealmCompanion\Navigation\Navigation;
use GreatMarketrealmCompanion\Core\Http\Response;
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

        add_action(
            'admin_post_gmrc_app_request',
            [$this, 'handleApplicationRequest']
        );
    
        add_action(
            'admin_post_nopriv_gmrc_app_request',
            [$this, 'handleApplicationRequest']
        );
    }


    /**
     * Handle a Companion application form request.
     */
    public function handleApplicationRequest(): void
    {
        $method = strtoupper(
            $_SERVER['REQUEST_METHOD'] ?? 'GET'
        );
    
        $route = isset($_POST['gmrc_route'])
            && is_scalar($_POST['gmrc_route'])
                ? sanitize_text_field(
                    wp_unslash((string) $_POST['gmrc_route'])
                )
                : 'not set';
    
        error_log(
            sprintf(
                'GMRC admin-post request — method: %s, route: %s',
                $method,
                $route
            )
        );
    
        if ($method !== 'POST') {
            wp_safe_redirect(
                home_url('/companion/')
            );
    
            exit;
        }
    
        $submittedNonce = isset($_POST['gmrc_nonce'])
            && is_scalar($_POST['gmrc_nonce'])
                ? sanitize_text_field(
                    wp_unslash((string) $_POST['gmrc_nonce'])
                )
                : '';
    
        error_log(
            'POST keys: ' .
            implode(', ', array_keys($_POST))
        );
    
        error_log(
            'Nonce exists: ' .
            ($submittedNonce !== '' ? 'yes' : 'no')
        );
    
        error_log(
            'Nonce value: ' .
            ($submittedNonce !== '' ? $submittedNonce : 'missing')
        );
    
        error_log(
            'Nonce verification result: ' .
            (
                $submittedNonce !== ''
                && wp_verify_nonce(
                    $submittedNonce,
                    'gmrc_create_character'
                )
                    ? 'valid'
                    : 'invalid'
            )
        );
    
        if (
            $submittedNonce === ''
            || ! wp_verify_nonce(
                $submittedNonce,
                'gmrc_create_character'
            )
        ) {
            wp_die(
                esc_html__(
                    'The form request could not be verified.',
                    'great-marketrealm-companion'
                ),
                esc_html__(
                    'Invalid request',
                    'great-marketrealm-companion'
                ),
                [
                    'response' => 403,
                ]
            );
        }
    
        $result = $this->app
            ->make(AppController::class)
            ->handle();
    
        if ($result instanceof Response) {
            $result->send();
            exit;
        }
    
        echo $result; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
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
