<?php

namespace GreatMarketrealmCompanion\Core\Http;

defined('ABSPATH') || exit;

/**
 * HTTP Request.
 *
 * Provides a small abstraction around the current
 * PHP request environment.
 *
 * @package MarketrealmCompanion
 * @since 0.6.0
 */
class Request
{
    /**
     * Create a Request from the current PHP globals.
     */
    public static function capture(): self
    {
        return new self();
    }

    /**
     * Retrieve the effective HTTP method.
     */
    public function method(): string
    {
        $method = strtoupper(
            $_SERVER['REQUEST_METHOD'] ?? 'GET'
        );

        if ($method !== 'POST') {
            return $method;
        }

        $spoofedMethod = $this->input(
            '_method'
        );

        if (! is_string($spoofedMethod)) {
            return $method;
        }

        $spoofedMethod = strtoupper(
            sanitize_text_field($spoofedMethod)
        );

        return in_array(
            $spoofedMethod,
            ['PUT', 'PATCH', 'DELETE'],
            true
        )
            ? $spoofedMethod
            : $method;
    }

    /**
     * Retrieve the current request path.
     */
    public function path(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        $path = parse_url(
            $requestUri,
            PHP_URL_PATH
        );

        if (! is_string($path)) {
            return '/';
        }

        $path = '/' . trim($path, '/');

        return $path === '/'
            ? '/'
            : rtrim($path, '/');
    }

    /**
     * Retrieve an input value.
     */
    public function input(
        string $key,
        mixed $default = null
    ): mixed {
        if (array_key_exists($key, $_POST)) {
            return wp_unslash(
                $_POST[$key]
            );
        }

        if (array_key_exists($key, $_GET)) {
            return wp_unslash(
                $_GET[$key]
            );
        }

        return $default;
    }

    /**
     * Determine whether an input value exists.
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $_POST)
            || array_key_exists($key, $_GET);
    }

    /**
     * Retrieve all request input.
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return array_merge(
            wp_unslash($_GET),
            wp_unslash($_POST)
        );
    }

    /**
     * Retrieve a sanitised string input value.
     */
    public function string(
        string $key,
        string $default = ''
    ): string {
        $value = $this->input(
            $key,
            $default
        );

        if (! is_scalar($value)) {
            return $default;
        }

        return sanitize_text_field(
            (string) $value
        );
    }

    /**
     * Retrieve an integer input value.
     */
    public function integer(
        string $key,
        int $default = 0
    ): int {
        $value = $this->input(
            $key,
            $default
        );

        if (! is_scalar($value)) {
            return $default;
        }

        return absint($value);
    }

    /**
     * Determine whether the request uses a method.
     */
    public function isMethod(string $method): bool
    {
        return $this->method()
            === strtoupper($method);
    }
}
