<?php

namespace GreatMarketrealmCompanion\Controllers;

use GreatMarketrealmCompanion\View\View;

defined('ABSPATH') || exit;

/**
 * Base Controller
 *
 * All application controllers should extend this class.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.2.0-alpha3.1.1
 */
abstract class Controller
{
    /**
     * Render a view.
     *
     * @param string $view   The view to render.
     * @param array  $data   Data to pass to the view.
     * @param string $layout Layout to use.
     *
     * @return string
     */
    protected function view(
        string $view,
        array $data = [],
        string $layout = 'app'
    ): string {

        return View::render(
            $view,
            $data,
            $layout
        );
    }

    /**
     * Return a JSON response.
     *
     * Useful for future AJAX and REST endpoints.
     *
     * @param array $data
     *
     * @return string
     */
    protected function json(array $data): string
    {
        return wp_json_encode($data);
    }

    /**
     * Redirect to a URL.
     *
     * @param string $url
     * @param int    $status
     *
     * @return void
     */
    protected function redirect(
        string $url,
        int $status = 302
    ): void {

        wp_safe_redirect($url, $status);

        exit;
    }

    /**
     * Return the current logged in user.
     *
     * @return \WP_User
     */
    protected function user(): \WP_User
    {
        return wp_get_current_user();
    }

    /**
     * Determine whether the current user is logged in.
     *
     * @return bool
     */
    protected function isLoggedIn(): bool
    {
        return is_user_logged_in();
    }

    /**
     * Abort if the current user is not logged in.
     *
     * Future versions may redirect to a custom login page.
     *
     * @return void
     */
    protected function requireLogin(): void
    {
        if (! $this->isLoggedIn()) {

            auth_redirect();

            exit;
        }
    }
}
