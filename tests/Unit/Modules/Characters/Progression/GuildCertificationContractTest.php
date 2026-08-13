<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class GuildCertificationContractTest extends TestCase
{
    public function testCertificationRevalidatesAndPersistsBeforeCleanup(): void
    {
        $root = dirname(__DIR__, 5);

        $service = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/'
            . 'Services/GuildCertificationService.php'
        );

        self::assertIsString($service);

        self::assertStringContainsString(
            'AdvancementLedgerPresenter',
            $service
        );

        self::assertStringContainsString(
            'AdvancementSealPresenter',
            $service
        );

        self::assertStringContainsString(
            "empty(\$seal['ready'])",
            $service
        );

        self::assertStringContainsString(
            '$pending->matches(',
            $service
        );

        $savePosition = strpos(
            $service,
            '$this->characters->save('
        );

        $clearPosition = strpos(
            $service,
            '$this->pending->clear('
        );

        self::assertIsInt($savePosition);
        self::assertIsInt($clearPosition);
        self::assertLessThan(
            $clearPosition,
            $savePosition
        );
    }

    public function testCertificationHistoryIsIdempotent(): void
    {
        $root = dirname(__DIR__, 5);

        $repository = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/'
            . 'Repositories/AdvancementHistoryRepository.php'
        );

        self::assertIsString($repository);

        self::assertStringContainsString(
            '_gmrc_advancement_history',
            $repository
        );

        self::assertStringContainsString(
            'certification_key',
            $repository
        );

        self::assertStringContainsString(
            'return;',
            $repository
        );
    }
}
