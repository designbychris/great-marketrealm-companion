<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Presentation;

use PHPUnit\Framework\TestCase;

final class NarrativeInterfaceLocalisationRegressionTest extends TestCase
{
    public function test_auby_and_illuminator_narrative_interface_is_localisable(): void
    {
        $root = dirname(__DIR__, 3);
        $quotes = (string) file_get_contents($root . '/app/Services/Auby/QuoteRepository.php');
        $portrait = (string) file_get_contents($root . '/app/Views/components/media/illuminated-portrait.php');
        self::assertStringContainsString("__('Every page begins empty. That is what makes it full of possibility.', 'great-marketrealm-companion')", $quotes);
        self::assertStringContainsString("esc_html_e('Awaiting subject', 'great-marketrealm-companion')", $portrait);
    }
}
