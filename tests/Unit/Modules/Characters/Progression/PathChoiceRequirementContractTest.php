<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression;

use PHPUnit\Framework\TestCase;

final class PathChoiceRequirementContractTest extends TestCase
{
    public function testAdvancementChoiceResolverUnderstandsCallingPaths(): void
    {
        $root = dirname(__DIR__, 5);

        $resolver = file_get_contents(
            $root
            . '/app/Modules/Characters/Progression/'
            . 'Services/AdvancementChoiceRequirementResolver.php'
        );

        self::assertIsString($resolver);

        self::assertStringContainsString(
            'PathProgressionCatalogue',
            $resolver
        );

        self::assertStringContainsString(
            'PathCandidateCatalogue',
            $resolver
        );

        self::assertStringContainsString(
            'ChoiceMode::SINGLE',
            $resolver
        );

        self::assertStringContainsString(
            "['choice_key']",
            $resolver
        );
    }
}
