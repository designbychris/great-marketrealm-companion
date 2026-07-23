<?php

namespace GreatMarketrealmCompanion\Core\Http;

defined('ABSPATH') || exit;

/**
 * JSON HTTP Response.
 *
 * Encodes structured response data as JSON.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.6.0
 */
class JsonResponse extends Response
{
    /**
     * Original response data.
     */
    protected mixed $data;

    /**
     * Create a JSON response.
     *
     * @param array<string, string> $headers
     */
    public function __construct(
        mixed $data = null,
        int $status = 200,
        array $headers = []
    ) {
        $this->data = $data;

        $headers = array_merge(
            [
                'Content-Type' =>
                    'application/json; charset=UTF-8',
            ],
            $headers
        );

        parent::__construct(
            $this->encode($data),
            $status,
            $headers
        );
    }

    /**
     * Retrieve the original response data.
     */
    public function data(): mixed
    {
        return $this->data;
    }

    /**
     * Encode response data as JSON.
     */
    protected function encode(
        mixed $data
    ): string {
        $json = wp_json_encode(
            $data,
            JSON_UNESCAPED_SLASHES
            | JSON_UNESCAPED_UNICODE
        );

        if ($json !== false) {
            return $json;
        }

        return wp_json_encode(
            [
                'success' => false,
                'message' =>
                    'The response could not be encoded.',
            ]
        ) ?: '{}';
    }
}
