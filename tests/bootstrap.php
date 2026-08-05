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

if (! function_exists('esc_html')) {
    function esc_html(mixed $text): string
    {
        return htmlspecialchars(
            (string) $text,
            ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5,
            'UTF-8'
        );
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

if (! function_exists('__')) {
    function __(
        string $text,
        string $domain = 'default'
    ): string {
        return $text;
    }
}

if (! function_exists('get_permalink')) {
    function get_permalink(): string
    {
        return 'https://example.test/companion/';
    }
}

if (! function_exists('home_url')) {
    function home_url(string $path = ''): string
    {
        return 'https://example.test' . $path;
    }
}

if (! function_exists('add_query_arg')) {
    function add_query_arg(
        string $key,
        string $value,
        string $url
    ): string {
        return $url . '?'
            . urlencode($key)
            . '='
            . urlencode($value);
    }
}

if (! function_exists('remove_query_arg')) {
    function remove_query_arg(
        string $key,
        string $url
    ): string {
        return $url;
    }
}

$loader = require dirname(__DIR__) . '/vendor/autoload.php';

$loader->setClassMapAuthoritative(false);

spl_autoload_register(
    function (string $class) {
        file_put_contents(
            __DIR__ . '/autoload.log',
            $class . PHP_EOL,
            FILE_APPEND
        );
    },
    true,
    true
);
