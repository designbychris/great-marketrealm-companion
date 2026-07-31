<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Stubs;

use GreatMarketrealmCompanion\Core\Pages\Page;
use GreatMarketrealmCompanion\Resources\Resource;

final class TestPage extends Page
{
    public function __construct(
        Resource $resource,
        private readonly string $httpMethod = 'GET'
    ) {
        parent::__construct($resource);
    }

    public function key(): string
    {
        return 'dashboard';
    }

    public function title(): string
    {
        return 'Dashboard';
    }

    public function path(): string
    {
        return '/dashboard';
    }

    /**
     * @return callable
     */
    public function handler(): callable
    {
        return static fn (): string => 'OK';
    }

    public function method(): string
    {
        return $this->httpMethod;
    }
}
