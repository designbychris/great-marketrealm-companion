<?php

declare(strict_types=1);

if (! defined('ABSPATH')) {
    define('ABSPATH', dirname(__DIR__) . '/');
}

/*
 * Minimal WordPress function stubs required by unit tests.
 */
if (! function_exists('status_header')) {
    function status_header(
        int $code,
        string $description = ''
    ): void {
        // Intentionally empty during unit testing.
    }
}

if (! function_exists('wp_die')) {
    function wp_die(
        string $message = '',
        string $title = '',
        array|int $args = []
    ): never {
        throw new RuntimeException($message);
    }
}

require dirname(__DIR__) . '/vendor/autoload.php';
