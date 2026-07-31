<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Stubs;

use GreatMarketrealmCompanion\Core\Application;
use GreatMarketrealmCompanion\Resources\Resource;
use GreatMarketrealmCompanion\Tests\Stubs\TestController;

final class TestResource extends Resource
{
    public function __construct(
        Application $app
    ) {
        parent::__construct($app);
    }

    public function key(): string
    {
        return 'characters';
    }

    public function singularName(): string
    {
        return 'Character';
    }

    public function pluralName(): string
    {
        return 'Characters';
    }

    public function routePrefix(): string
    {
        return '/characters';
    }

    public function controller(): string
    {
        return TestController::class;
    }
}
