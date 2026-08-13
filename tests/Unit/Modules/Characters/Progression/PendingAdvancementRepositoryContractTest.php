<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class PendingAdvancementRepositoryContractTest extends TestCase
{
    public function testPendingAdvancementUsesCharacterPostMeta(): void
    {
        $root = dirname(__DIR__, 5);

        $repository = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/'
            . 'Repositories/PendingAdvancementRepository.php'
        );

        self::assertIsString($repository);

        self::assertStringContainsString(
            "_gmrc_pending_advancement",
            $repository
        );

        self::assertStringContainsString(
            "update_post_meta(",
            $repository
        );

        self::assertStringContainsString(
            "get_post_meta(",
            $repository
        );

        self::assertStringContainsString(
            "'post_type' => 'gmrc_character'",
            $repository
        );

        self::assertStringContainsString(
            "'author' => get_current_user_id()",
            $repository
        );
    }

    public function testControllerUsesDurablePendingAdvancement(): void
    {
        $root = dirname(__DIR__, 5);

        $controller = file_get_contents(
            $root
            . '/app/Modules/Characters/Controllers/'
            . 'CharacterController.php'
        );

        self::assertIsString($controller);

        self::assertStringContainsString(
            'new PendingAdvancementRepository()',
            $controller
        );

        self::assertStringContainsString(
            '->resumeOrBegin(',
            $controller
        );

        self::assertStringContainsString(
            '->recordChoice(',
            $controller
        );

        self::assertStringContainsString(
            'new AdvancementSealPresenter()',
            $controller
        );
    }
}
