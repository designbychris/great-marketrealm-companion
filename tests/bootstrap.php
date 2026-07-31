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

if (! function_exists('esc_html__')) {
    function esc_html__(
        string $text,
        string $domain = 'default'
    ): string {
        return $text;
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

if (! function_exists('wp_unslash')) {
    function wp_unslash(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(
                'wp_unslash',
                $value
            );
        }

        return is_string($value)
            ? stripslashes($value)
            : $value;
    }
}

if (! function_exists('sanitize_text_field')) {
    function sanitize_text_field(string $value): string
    {
        $value = strip_tags($value);

        return trim($value);
    }
}

if (! function_exists('absint')) {
    function absint(mixed $value): int
    {
        return abs((int) $value);
    }
}

require dirname(__DIR__) . '/vendor/autoload.php';
