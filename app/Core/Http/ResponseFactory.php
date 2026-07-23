<?php

namespace GreatMarketrealmCompanion\Core\Http;

defined('ABSPATH') || exit;

/**
 * HTTP Response Factory.
 *
 * Creates standard application responses.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.6.0
 */
class ResponseFactory
{
    /**
     * Create a standard HTML or text response.
     *
     * @param array<string, string> $headers
     */
    public function make(
        string $content = '',
        int $status = 200,
        array $headers = []
    ): Response {
        return new Response(
            $content,
            $status,
            $headers
        );
    }

    /**
     * Create a redirect response.
     */
    public function redirect(
        string $destination,
        int $status = 303
    ): RedirectResponse {
        if (! $this->isAbsoluteUrl($destination)) {
            $destination = home_url(
                '/' . ltrim($destination, '/')
            );
        }

        return new RedirectResponse(
            $destination,
            $status
        );
    }

    /**
     * Create a JSON response.
     *
     * @param array<string, string> $headers
     */
    public function json(
        mixed $data = null,
        int $status = 200,
        array $headers = []
    ): JsonResponse {
        return new JsonResponse(
            $data,
            $status,
            $headers
        );
    }

    /**
     * Create a response indicating that a resource was not found.
     */
    public function notFound(
        string $message = 'Resource not found.'
    ): Response {
        return new Response(
            $message,
            404,
            [
                'Content-Type' =>
                    'text/plain; charset=UTF-8',
            ]
        );
    }

    /**
     * Create a response with no body.
     */
    public function noContent(): Response
    {
        return new Response(
            '',
            204
        );
    }

    /**
     * Determine whether a destination is an absolute URL.
     */
    protected function isAbsoluteUrl(
        string $destination
    ): bool {
        return filter_var(
            $destination,
            FILTER_VALIDATE_URL
        ) !== false;
    }
}
