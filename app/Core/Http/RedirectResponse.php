<?php

namespace GreatMarketrealmCompanion\Core\Http;

defined('ABSPATH') || exit;

/**
 * HTTP Redirect Response.
 *
 * Represents a redirect to another application URL.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.6.0
 */
class RedirectResponse extends Response
{
    /**
     * Redirect destination.
     */
    protected string $destination;

    /**
     * Create a redirect response.
     */
    public function __construct(
        string $destination,
        int $status = 302
    ) {
        $this->destination = wp_validate_redirect(
            $destination,
            home_url('/')
        );

        parent::__construct(
            '',
            $this->validStatus($status),
            [
                'Location' => $this->destination,
            ]
        );
    }

    /**
     * Retrieve the redirect destination.
     */
    public function destination(): string
    {
        return $this->destination;
    }

    /**
     * Ensure the status is a redirect status.
     */
    protected function validStatus(
        int $status
    ): int {
        return in_array(
            $status,
            [301, 302, 303, 307, 308],
            true
        )
            ? $status
            : 302;
    }
}
