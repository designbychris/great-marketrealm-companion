<?php

namespace GreatMarketrealmCompanion\Core\Http;

defined('ABSPATH') || exit;

/**
 * HTTP Response.
 *
 * Represents content returned to the browser,
 * together with its status and headers.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.6.0
 */
class Response
{
    /**
     * Response content.
     */
    protected string $content;

    /**
     * HTTP status code.
     */
    protected int $status;

    /**
     * Response headers.
     *
     * @var array<string, string>
     */
    protected array $headers;

    /**
     * Create an HTTP response.
     *
     * @param array<string, string> $headers
     */
    public function __construct(
        string $content = '',
        int $status = 200,
        array $headers = []
    ) {
        $this->content = $content;
        $this->status = $status;
        $this->headers = $headers;
    }

    /**
     * Retrieve the response content.
     */
    public function content(): string
    {
        return $this->content;
    }

    /**
     * Retrieve the HTTP status code.
     */
    public function status(): int
    {
        return $this->status;
    }

    /**
     * Retrieve all response headers.
     *
     * @return array<string, string>
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Replace the response content.
     */
    public function setContent(
        string $content
    ): static {
        $this->content = $content;

        return $this;
    }

    /**
     * Replace the HTTP status code.
     */
    public function setStatus(
        int $status
    ): static {
        $this->status = $status;

        return $this;
    }

    /**
     * Add or replace a response header.
     */
    public function withHeader(
        string $name,
        string $value
    ): static {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Send the response headers.
     */
    public function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        status_header(
            $this->status
        );

        foreach ($this->headers as $name => $value) {
            header(
                sprintf(
                    '%s: %s',
                    $name,
                    $value
                ),
                true
            );
        }
    }

    /**
     * Send the response to the browser.
     */
    public function send(): void
    {
        $this->sendHeaders();

        echo $this->content;
    }
}
