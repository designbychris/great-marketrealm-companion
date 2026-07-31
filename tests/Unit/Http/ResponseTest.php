<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Http;

use GreatMarketrealmCompanion\Core\Http\Response;
use PHPUnit\Framework\TestCase;

final class ResponseTest extends TestCase
{
    public function testResponseCanBeCreatedWithDefaults(): void
    {
        $response = new Response();

        $this->assertSame(
            '',
            $response->content()
        );

        $this->assertSame(
            200,
            $response->status()
        );

        $this->assertSame(
            [],
            $response->headers()
        );
    }

    public function testResponseCanBeCreatedWithContent(): void
    {
        $response = new Response(
            'Hello Marketrealm'
        );

        $this->assertSame(
            'Hello Marketrealm',
            $response->content()
        );
    }

    public function testResponseCanBeCreatedWithStatus(): void
    {
        $response = new Response(
            'Created',
            201
        );

        $this->assertSame(
            201,
            $response->status()
        );
    }

    public function testResponseCanBeCreatedWithHeaders(): void
    {
        $response = new Response(
            'Created',
            201,
            [
                'Content-Type' => 'application/json',
                'X-Marketrealm' => 'Auby',
            ]
        );

        $this->assertSame(
            [
                'Content-Type' => 'application/json',
                'X-Marketrealm' => 'Auby',
            ],
            $response->headers()
        );
    }

    public function testResponseContentCanBeReplaced(): void
    {
        $response = new Response(
            'Original'
        );

        $response->setContent(
            'Updated'
        );

        $this->assertSame(
            'Updated',
            $response->content()
        );
    }

    public function testSetContentReturnsSameResponseInstance(): void
    {
        $response = new Response();

        $result = $response->setContent(
            'Updated'
        );

        $this->assertSame(
            $response,
            $result
        );
    }

    public function testResponseStatusCanBeReplaced(): void
    {
        $response = new Response();

        $response->setStatus(
            404
        );

        $this->assertSame(
            404,
            $response->status()
        );
    }

    public function testSetStatusReturnsSameResponseInstance(): void
    {
        $response = new Response();

        $result = $response->setStatus(
            201
        );

        $this->assertSame(
            $response,
            $result
        );
    }

    public function testResponseHeaderCanBeAdded(): void
    {
        $response = new Response();

        $response->withHeader(
            'Content-Type',
            'application/json'
        );

        $this->assertSame(
            [
                'Content-Type' => 'application/json',
            ],
            $response->headers()
        );
    }

    public function testResponseHeaderCanBeReplaced(): void
    {
        $response = new Response(
            headers: [
                'Content-Type' => 'text/html',
            ]
        );

        $response->withHeader(
            'Content-Type',
            'application/json'
        );

        $this->assertSame(
            [
                'Content-Type' => 'application/json',
            ],
            $response->headers()
        );
    }

    public function testWithHeaderReturnsSameResponseInstance(): void
    {
        $response = new Response();

        $result = $response->withHeader(
            'Content-Type',
            'application/json'
        );

        $this->assertSame(
            $response,
            $result
        );
    }

    public function testResponseCanBeFluentlyConfigured(): void
    {
        $response = (new Response())
            ->setContent('Created')
            ->setStatus(201)
            ->withHeader(
                'Content-Type',
                'application/json'
            );

        $this->assertSame(
            'Created',
            $response->content()
        );

        $this->assertSame(
            201,
            $response->status()
        );

        $this->assertSame(
            [
                'Content-Type' => 'application/json',
            ],
            $response->headers()
        );
    }

    public function testSendOutputsResponseContent(): void
    {
        $response = new Response(
            'Welcome to the Marketrealm'
        );

        ob_start();

        $response->send();

        $output = ob_get_clean();

        $this->assertSame(
            'Welcome to the Marketrealm',
            $output
        );
    }

    public function testSendOutputsEmptyContent(): void
    {
        $response = new Response();

        ob_start();

        $response->send();

        $output = ob_get_clean();

        $this->assertSame(
            '',
            $output
        );
    }

    public function testSendHeadersDoesNotAlterResponseData(): void
    {
        $response = new Response(
            'Created',
            201,
            [
                'Content-Type' => 'application/json',
            ]
        );

        $response->sendHeaders();

        $this->assertSame(
            'Created',
            $response->content()
        );

        $this->assertSame(
            201,
            $response->status()
        );

        $this->assertSame(
            [
                'Content-Type' => 'application/json',
            ],
            $response->headers()
        );
    }
}
