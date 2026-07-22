<?php

namespace GreatMarketrealmCompanion\Providers;

defined('ABSPATH') || exit;

/**
 * Front-end Service Provider.
 *
 * Registers the Marketrealm Companion application shell.
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
        // No container bindings are required yet.
    }

    /**
     * Boot front-end functionality.
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
     * Load Companion front-end assets.
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
     * Render the Companion application.
     */
    public function renderApp(): string
    {
        $dashboardView = GMRC_PATH
            . 'app/Modules/Dashboard/Views/index.php';

        $layoutView = GMRC_PATH
            . 'app/Views/layouts/app.php';

        if (
            ! file_exists($dashboardView)
            || ! file_exists($layoutView)
        ) {
            return '<p>Marketrealm Companion views could not be loaded.</p>';
        }

        $content = $this->captureView($dashboardView);

        return $this->captureView(
            $layoutView,
            [
                'pageTitle' => 'Dashboard',
                'content'   => $content,
            ]
        );
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
            && has_shortcode($post->post_content, 'gmrc_app');
    }

    /**
     * Capture a PHP view and return its HTML.
     *
     * @param string               $viewPath View file path.
     * @param array<string, mixed> $data     View variables.
     */
    protected function captureView(
        string $viewPath,
        array $data = []
    ): string {
        extract($data, EXTR_SKIP);

        ob_start();

        require $viewPath;

        return (string) ob_get_clean();
    }
}
