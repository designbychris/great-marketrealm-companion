<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Integration\Expansions;

use GreatMarketrealmCompanion\Integration\Expansions\ExpansionCharacterCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Repositories\CharacterCatalogueRepository;
use GreatMarketrealmCompanion\Modules\Characters\Catalogue\Services\SubclassPreviewCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Background;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use GreatMarketrealmCompanion\Modules\Library\Backgrounds\Repositories\BackgroundMechanicsRegister;
use PHPUnit\Framework\TestCase;

final class ExpansionCharacterCatalogueTest extends TestCase
{
    public function test_it_gracefully_degrades_when_active_content_is_unavailable(): void
    {
        $catalogue = new ExpansionCharacterCatalogue(static fn (): mixed => null);

        self::assertFalse($catalogue->available());
        self::assertSame([], $catalogue->races());
        self::assertSame([], $catalogue->backgrounds());
        self::assertSame([], $catalogue->subclasses());
    }

    public function test_it_rejects_an_incompatible_active_content_api(): void
    {
        $active = new class {
            public function apiVersion(): string { return '0.9.9'; }
            public function ofType(string $type): array { return []; }
        };

        $catalogue = new ExpansionCharacterCatalogue(static fn () => $active);

        self::assertFalse($catalogue->available());
        self::assertSame([], $catalogue->races());
    }

    public function test_it_projects_active_content_with_fully_qualified_provenance(): void
    {
        $catalogue = $this->catalogue([
            $this->entry('midnight-menu', 'race', 'pizzakin', [
                'name' => 'Pizzakin',
                'creature_type' => 'Humanoid',
                'size' => ['options' => ['Small', 'Medium']],
                'speed' => ['walk' => 30],
                'languages' => ['source-not-specified'],
                'traits' => [],
            ]),
        ]);

        $races = $catalogue->races();

        self::assertArrayHasKey('pizzakin', $races);
        self::assertSame('Pizzakin', $races['pizzakin']['name']);
        self::assertSame('midnight-menu:race:pizzakin', $races['pizzakin']['canonical_id']);
        self::assertSame('midnight-menu', $races['pizzakin']['expansion']);
        self::assertSame('gmrexp', $races['pizzakin']['source_kind']);
        self::assertSame('midnight-menu:race:pizzakin', $catalogue->canonicalId('race', 'pizzakin'));
    }

    public function test_it_refuses_to_guess_when_two_active_almanacs_share_a_local_key(): void
    {
        $catalogue = $this->catalogue([
            $this->entry('book-one', 'race', 'shared-folk', ['name' => 'First Shared Folk']),
            $this->entry('book-two', 'race', 'shared-folk', ['name' => 'Second Shared Folk']),
        ]);

        self::assertSame([], $catalogue->races());
        self::assertNull($catalogue->canonicalId('race', 'shared-folk'));
    }

    public function test_character_catalogue_augments_native_choices_without_replacing_them(): void
    {
        $expansions = $this->catalogue([
            $this->entry('midnight-menu', 'race', 'pizzakin', ['name' => 'Pizzakin']),
            $this->entry('midnight-menu', 'subclass', 'path-of-the-hangry', [
                'name' => 'Path of the Hangry',
                'parent_class' => 'barbarian',
                'entry_level' => 3,
                'features' => [],
                'progression' => [],
            ]),
        ]);

        $characters = new CharacterCatalogueRepository($expansions);
        $races = $characters->raceOptions();
        $subclasses = $characters->subclasses();

        self::assertArrayHasKey('fructan', $races);
        self::assertSame('Pizzakin', $races['pizzakin']);
        self::assertNotEmpty(array_filter(
            $subclasses,
            static fn (array $item): bool => ($item['key'] ?? '') === 'path-of-the-hangry'
                && ($item['parent'] ?? '') === 'barbarian'
                && ($item['canonical_id'] ?? '') === 'midnight-menu:subclass:path-of-the-hangry'
        ));
    }

    public function test_expansion_backgrounds_are_projected_into_registration_mechanics(): void
    {
        $expansions = $this->catalogue([
            $this->entry('midnight-menu', 'background', 'former-fry-cook', [
                'name' => 'Former Fry Cook',
                'proficiencies' => [
                    'skills' => ['Athletics'],
                    'tools' => ["Cook's Utensils"],
                ],
                'starting_equipment' => [],
                'features' => [[
                    'key' => 'kitchen-reflexes',
                    'name' => 'Kitchen Reflexes',
                    'description' => 'Advantage against mundane kitchen hazards.',
                ]],
            ]),
        ]);

        $register = new BackgroundMechanicsRegister(null, $expansions);
        $records = array_filter(
            $register->all(),
            static fn ($record): bool => $record->key() === 'former-fry-cook'
        );
        $record = array_values($records)[0] ?? null;

        self::assertNotNull($record);
        self::assertSame(['athletics'], $record->skills());
        self::assertSame(['cooks-utensils'], $record->tools());
        self::assertSame('Kitchen Reflexes', $record->featureName());
        self::assertSame('midnight-menu:background:former-fry-cook', $record->canonicalId());
    }

    public function test_race_and_background_snapshots_keep_existing_characters_readable_after_deactivation(): void
    {
        $race = Race::fromStringWithLabel('pizzakin', 'Pizzakin');
        $background = Background::fromStringWithMechanics(
            'former-fry-cook',
            ['athletics'],
            ['cooks-utensils'],
            'Former Fry Cook',
            0,
            []
        );

        self::assertSame('pizzakin', $race->value());
        self::assertSame('Pizzakin', $race->label());
        self::assertSame('Former Fry Cook', $background->label());
        self::assertSame(['athletics'], $background->skillProficiencies()->proficiencies());
        self::assertSame(['cooks-utensils'], $background->toolProficiencyIdentifiers());
        self::assertSame(0, $background->languageChoices());
    }

    public function test_subclass_preview_keeps_expansion_progression_as_a_read_only_projection(): void
    {
        $path = dirname(__DIR__, 4) . '/app/Modules/Characters/Catalogue/Services/SubclassPreviewCatalogue.php';
        $source = file_get_contents($path);

        self::assertIsString($source);
        self::assertStringContainsString('expansionGiftPreview($subclass)', $source);
        self::assertStringContainsString("(\$subclass['source_kind'] ?? '') !== 'gmrexp'", $source);
        self::assertStringContainsString("\$subclass['progression']", $source);
    }

    /** @param array<int,object> $entries */
    public function testCharactersProviderRegistersExpansionAdapterWithoutAutowiringClosure(): void
    {
        $source = file_get_contents(
            dirname(__DIR__, 4) . '/app/Modules/Characters/CharactersServiceProvider.php'
        );

        self::assertIsString($source);
        self::assertStringContainsString('ExpansionCharacterCatalogue::class', $source);
        self::assertStringContainsString('new ExpansionCharacterCatalogue()', $source);
    }

    private function catalogue(array $entries): ExpansionCharacterCatalogue
    {
        $active = new class($entries) {
            public function __construct(private array $entries) {}
            public function apiVersion(): string { return '1.0.0'; }
            public function ofType(string $type): array
            {
                return array_values(array_filter(
                    $this->entries,
                    static fn (object $entry): bool => $entry->type() === $type
                ));
            }
        };

        return new ExpansionCharacterCatalogue(static fn () => $active);
    }

    /** @param array<string,mixed> $data */
    private function entry(string $expansion, string $type, string $key, array $data): object
    {
        return new class($expansion, $type, $key, $data) {
            public function __construct(
                private string $expansion,
                private string $type,
                private string $key,
                private array $data
            ) {}
            public function key(): string { return $this->key; }
            public function type(): string { return $this->type; }
            public function id(): string { return $this->expansion . ':' . $this->type . ':' . $this->key; }
            public function expansionKey(): string { return $this->expansion; }
            public function data(): array { return $this->data; }
        };
    }
}
