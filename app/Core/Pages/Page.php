<?php

namespace GreatMarketrealmCompanion\Core\Pages;

use GreatMarketrealmCompanion\Resources\Resource;

defined('ABSPATH') || exit;

abstract class Page
{
    public function __construct(
        protected Resource $resource
    ) {
    }

    abstract public function key(): string;

    abstract public function title(): string;

    abstract public function route(): string;

    public function method(): string
    {
        return 'GET';
    }

    public function resource(): Resource
    {
        return $this->resource;
    }
}
