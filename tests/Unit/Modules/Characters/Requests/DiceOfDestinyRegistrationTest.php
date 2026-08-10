<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Requests;

use PHPUnit\Framework\TestCase;

final class DiceOfDestinyRegistrationTest extends TestCase
{
    public function testRegistrationConcernSeparatesStandardAndRolledRules(): void
    {
        $root = dirname(__DIR__, 5);
        $source = file_get_contents(
            $root
            . '/app/Modules/Characters/Requests/Concerns/'
            . 'ResolvesRegistrationInput.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString(
            'registrationAbilityMethod',
            $source
        );
        self::assertStringContainsString(
            "$method === 'rolled'",
            $source
        );
        self::assertStringContainsString(
            '$score < 3 || $score > 18',
            $source
        );
        self::assertStringContainsString(
            "$method === 'standard'",
            $source
        );
        self::assertStringContainsString(
            'Assign each Standard Guild Array value exactly once.',
            $source
        );
    }
}
