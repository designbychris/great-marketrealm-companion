<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Library;

use PHPUnit\Framework\TestCase;

final class GuildFieldGuideRegressionTest extends TestCase
{
    public function testFieldGuideHasDedicatedGuildLibraryRoutes(): void
    {
        $routes = $this->file('app/Modules/Library/Routes.php');

        self::assertStringContainsString("'/library/field-guide'", $routes);
        self::assertStringContainsString("'/library/field-guide/{monsterKey}'", $routes);
        self::assertStringContainsString("[LibraryController::class, 'fieldGuide']", $routes);
        self::assertStringContainsString("[LibraryController::class, 'fieldGuideEntry']", $routes);
    }

    public function testFieldGuideProjectionWhitelistsPlayerSafeFieldsOnly(): void
    {
        $service = $this->file('app/Modules/Library/FieldGuide/Services/GuildFieldGuide.php');

        foreach ([
            "'key' =>",
            "'name' =>",
            "'creature_type' =>",
            "'size' =>",
            "'description' =>",
            "'image_attachment_id' =>",
        ] as $safeField) {
            self::assertStringContainsString($safeField, $service);
        }

        foreach ([
            "'armor_class' =>",
            "'max_hp' =>",
            "'challenge' =>",
            "'traits' =>",
            "'actions' =>",
            "'legendary_actions' =>",
            "'damage_resistances' =>",
        ] as $dmField) {
            self::assertStringNotContainsString($dmField, $service);
        }
    }

    public function testOnlyStewardApprovedCreaturesCanEnterFieldGuide(): void
    {
        $service = $this->file('app/Modules/Library/FieldGuide/Services/GuildFieldGuide.php');
        $monster = $this->file('app/Modules/DungeonMaster/Bestiary/Models/CanonicalMonster.php');

        self::assertStringContainsString('fieldGuideVisible()', $service);
        self::assertStringContainsString('playerDescription()', $service);
        self::assertStringContainsString('public function fieldGuideVisible(): bool', $monster);
        self::assertStringContainsString('public function playerDescription(): string', $monster);
    }

    public function testStewardCanPublishSafeLoreWithoutPublishingMechanics(): void
    {
        $view = $this->file('app/Modules/Administration/Views/canonical-records.php');
        $steward = $this->file('app/Modules/Administration/CanonicalRecords/CanonicalBestiarySteward.php');

        self::assertStringContainsString('name="field_guide_visible"', $view);
        self::assertStringContainsString('name="player_description"', $view);
        self::assertStringContainsString('Visible in the Guild Field Guide', $view);
        self::assertStringContainsString("'field_guide_visible' => ! empty", $steward);
        self::assertStringContainsString("'player_description' => sanitize_textarea_field", $steward);
    }

    public function testGuildLibraryRegistersAndLinksTheFieldGuide(): void
    {
        $provider = $this->file('app/Modules/Library/LibraryServiceProvider.php');
        $index = $this->file('app/Modules/Library/Views/index.php');
        $catalogue = $this->file('app/Modules/Library/Catalogues/FieldGuideReferenceCatalogue.php');

        self::assertStringContainsString('FieldGuideReferenceCatalogue', $provider);
        self::assertStringContainsString("return 'field-guide';", $catalogue);
        self::assertStringContainsString('Open Guild Field Guide', $index);
        self::assertStringContainsString("'library/field-guide'", $index);
    }

    public function testFieldGuideViewsNeverRenderDungeonMasterStatistics(): void
    {
        $index = $this->file('app/Modules/Library/Views/field-guide/index.php');
        $show = $this->file('app/Modules/Library/Views/field-guide/show.php');
        $views = $index . "\n" . $show;

        foreach ([
            'armorClass(',
            'maxHp(',
            'challenge(',
            'traits(',
            'actions(',
            'legendaryActions(',
            'damageResistances(',
        ] as $mechanic) {
            self::assertStringNotContainsString($mechanic, $views);
        }

        self::assertStringContainsString('Combat records remain sealed', $show);
    }

    public function testFieldGuideIncludesSearchAccessibilityAndResponsiveTreatment(): void
    {
        $index = $this->file('app/Modules/Library/Views/field-guide/index.php');
        $show = $this->file('app/Modules/Library/Views/field-guide/show.php');
        $css = $this->file('assets/css/modules/library/guild-library.css');

        self::assertStringContainsString('role="search"', $index);
        self::assertStringContainsString('aria-live="polite"', $index);
        self::assertStringContainsString('aria-labelledby="gmrc-field-guide-folio-title"', $show);
        self::assertStringContainsString('@media (max-width: 720px)', $css);
        self::assertStringContainsString('@media (prefers-reduced-motion: reduce)', $css);
        self::assertStringContainsString('@media (forced-colors: active)', $css);
    }

    private function file(string $path): string
    {
        $contents = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
        self::assertIsString($contents, 'Expected source file to be readable: ' . $path);

        return $contents;
    }
}
