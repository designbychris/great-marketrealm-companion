<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Views;

use PHPUnit\Framework\TestCase;

final class LivingGuildSeasonContractTest extends TestCase
{
    public function testSeasonHooksCoverEntireYear(): void
    {
        $root = dirname(__DIR__, 3);

        $manifest = json_decode(
            (string) file_get_contents(
                $root
                . '/assets/data/guild-hall/'
                . 'living-guild.json'
            ),
            true
        );

        self::assertIsArray($manifest);

        $months = [];

        foreach (
            $manifest['seasons'] ?? []
            as $seasonMonths
        ) {
            $months = array_merge(
                $months,
                $seasonMonths
            );
        }

        sort($months);

        self::assertSame(
            range(1, 12),
            array_values(
                array_unique($months)
            )
        );
    }
}
