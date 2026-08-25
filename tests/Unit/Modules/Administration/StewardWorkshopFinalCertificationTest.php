<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;

use PHPUnit\Framework\TestCase;

final class StewardWorkshopFinalCertificationTest extends TestCase
{
    public function testCertificationAuditsSevenContentFamilies(): void
    {
        $source = $this->source(
            'app/Modules/Administration/Workshop/StewardWorkshopCertification.php'
        );

        foreach ([
            'Monsters',
            'Spells',
            'Backgrounds',
            'Equipment & Items',
            'Callings & Paths',
            'Folk',
            'Heritages',
        ] as $family) {
            self::assertStringContainsString("'" . $family . "'", $source);
        }

        self::assertStringContainsString(
            'count($families) === 7',
            $source
        );
    }

    public function testPublishedProjectionMustMatchPublishedLifecycleCount(): void
    {
        $source = $this->source(
            'app/Modules/Administration/Workshop/StewardWorkshopCertification.php'
        );

        self::assertStringContainsString(
            '$projectionCount === $counts[\'published\']',
            $source
        );
        self::assertStringContainsString(
            "'invalid' => \$invalid",
            $source
        );
    }

    public function testHeritagesInheritParentFolkLifecycle(): void
    {
        $source = $this->source(
            'app/Modules/Administration/Workshop/StewardWorkshopCertification.php'
        );

        self::assertStringContainsString(
            'private function heritageFamily(',
            $source
        );
        self::assertStringContainsString(
            '$counts[$status] += $heritageCount;',
            $source
        );
        self::assertStringContainsString(
            'Heritages inherit their parent Folk lifecycle cleanly.',
            $source
        );
    }

    public function testStewardOfficeDisplaysContentHealthTable(): void
    {
        $source = $this->source(
            'app/Modules/Administration/Views/stewards-office.php'
        );

        self::assertStringContainsString(
            'Content Health &amp; Certification',
            $source
        );
        self::assertStringContainsString(
            'gmrc-stewards-office__content-health-table',
            $source
        );
        self::assertStringContainsString(
            'Live projection',
            $source
        );
        self::assertStringContainsString(
            'gmrc-content-health',
            $source
        );
    }

    public function testCertificationPolicyKeepsDraftPublishedAndArchivedBoundariesExplicit(): void
    {
        $source = $this->source(
            'app/Modules/Administration/Workshop/StewardWorkshopCertification.php'
        );

        self::assertStringContainsString(
            'Draft content remains private to the Steward.',
            $source
        );
        self::assertStringContainsString(
            'Published content may enter Companion catalogues.',
            $source
        );
        self::assertStringContainsString(
            'Archived content is retired without destructive loss.',
            $source
        );
        self::assertStringContainsString(
            'Permanent deletion remains dependency-guarded.',
            $source
        );
    }

    private function source(string $relative): string
    {
        $root = dirname(__DIR__, 4);
        $source = file_get_contents($root . '/' . $relative);
        self::assertIsString($source);

        return $source;
    }
}
