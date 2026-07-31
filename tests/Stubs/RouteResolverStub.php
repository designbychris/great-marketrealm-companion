<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Stubs;

use GreatMarketrealmCompanion\Core\Http\RouteResolver;

final class RouteResolverStub extends RouteResolver
{
    public function __construct(
        private string $route = 'dashboard'
    ) {
    }

    public function current(): string
    {
        return $this->route;
    }
}
