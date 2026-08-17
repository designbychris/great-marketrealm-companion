<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Kingdoms;

use GreatMarketrealmCompanion\Modules\Parties\PartiesServiceProvider;

defined('ABSPATH') || exit;

final class PartiesKingdom extends Kingdom
{
    public function key(): string
    {
        return 'parties';
    }

    public function provider(): string
    {
        return PartiesServiceProvider::class;
    }
}
