<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Arcana\Services;

use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityDefinition;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;

defined('ABSPATH') || exit;

/**
 * Builds the Spells & Abilities Ledger pages from Character mechanics.
 */
final class ArcanePantryPresenter
{
    private ArcaneRollScalingResolver $rollScaling;

    /** @var array<int,array<int,int>> */
    private const FULL_CASTER_SLOTS = [
        1=>[2,0,0,0,0,0,0,0,0], 2=>[3,0,0,0,0,0,0,0,0],
        3=>[4,2,0,0,0,0,0,0,0], 4=>[4,3,0,0,0,0,0,0,0],
        5=>[4,3,2,0,0,0,0,0,0], 6=>[4,3,3,0,0,0,0,0,0],
        7=>[4,3,3,1,0,0,0,0,0], 8=>[4,3,3,2,0,0,0,0,0],
        9=>[4,3,3,3,1,0,0,0,0], 10=>[4,3,3,3,2,0,0,0,0],
        11=>[4,3,3,3,2,1,0,0,0], 12=>[4,3,3,3,2,1,0,0,0],
        13=>[4,3,3,3,2,1,1,0,0], 14=>[4,3,3,3,2,1,1,0,0],
        15=>[4,3,3,3,2,1,1,1,0], 16=>[4,3,3,3,2,1,1,1,0],
        17=>[4,3,3,3,2,1,1,1,1], 18=>[4,3,3,3,3,1,1,1,1],
        19=>[4,3,3,3,3,2,1,1,1], 20=>[4,3,3,3,3,2,2,1,1],
    ];
    private CanonicalSpellReferenceResolver $references;

    public function __construct(
        private ArcaneAbilityCatalogue $catalogue,
        ?ArcaneRollScalingResolver $rollScaling = null,
        ?CanonicalSpellReferenceResolver $references = null
    ) {
        $this->rollScaling =
            $rollScaling ?? new ArcaneRollScalingResolver();

        $this->references =
            $references ?? new CanonicalSpellReferenceResolver();
    }

    /** @return array<string, mixed> */
    public function present(Character $character): array
    {
        $class = $character->characterClass()->value();
        $castingAbility = $this->castingAbility($class);
        $castingModifier = $castingAbility === null
            ? 0
            : $character->abilityScores()
                ->all()[$castingAbility]
                ->modifier();

        $proficiency = $character->proficiencyBonus()->value();
        $spellAttack = $castingAbility === null
            ? null
            : $castingModifier + $proficiency;

        $saveDc = $castingAbility === null
            ? null
            : 8 + $castingModifier + $proficiency;

        $level = $character->level()->value();
        $maximumSpellLevel = $this->maximumSpellLevel(
            $class,
            $level
        );

        $available = array_values(
            array_filter(
                $this->catalogue->forClass($class),
                static fn (ArcaneAbilityDefinition $ability): bool =>
                    $ability->minimumLevel() <= $level
                    && (
                        $ability->kind() !== 'spell'
                        || (
                            $maximumSpellLevel > 0
                            && $ability->spellLevel()
                                <= $maximumSpellLevel
                        )
                    )
            )
        );

        $entries = array_map(
            fn (ArcaneAbilityDefinition $ability): array =>
                $this->entry(
                    $ability,
                    $castingModifier,
                    $spellAttack,
                    $saveDc,
                    $level,
                    $character->spellbook()->knows(
                        $ability->id()
                    )
                ),
            $available
        );

        return [
            'casting_ability' => $castingAbility === null
                ? null
                : ucfirst($castingAbility),
            'casting_modifier' => $castingModifier,
            'spell_attack' => $spellAttack,
            'save_dc' => $saveDc,
            'slots' => $this->slots(
                $class,
                $character->level()->value()
            ),
            'entries' => $entries,
            'shelves' => $this->indexedShelves($entries),
            'spellbook' => $character->spellbook()->toArray(),
            'has_spells' => count(
                array_filter(
                    $entries,
                    static fn (array $entry): bool =>
                        in_array(
                            $entry['kind'],
                            ['spell', 'cantrip'],
                            true
                        )
                )
            ) > 0,
        ];
    }

    /** @return array<string, mixed> */
    private function entry(
        ArcaneAbilityDefinition $ability,
        int $castingModifier,
        ?int $spellAttack,
        ?int $saveDc,
        int $level,
        bool $learned
    ): array {
        $modifier = 0;

        if ($ability->addCastingModifier()) {
            $modifier = $castingModifier;
        }

        if ($ability->id() === 'second-wind') {
            $modifier = $level;
        }

        $scaling = $this->rollScaling->resolve(
            $ability,
            $level,
            $ability->spellLevel() > 0
                ? $ability->spellLevel()
                : null
        );

        $reference = $this->references->resolve($ability);

        return [
            'id' => $ability->id(),
            'label' => (string) $reference['label'],
            'legacy_label' => (string) $reference['legacy_label'],
            'canonical_status' => (string) $reference['status'],
            'canonical_spell_key' => $reference['canonical_key'],
            'original_spell' => $reference['original_spell'],
            'source_issues' => $reference['source_issues'],
            'kind' => $ability->kind(),
            'description' => (string) $reference['detail'],
            'activation' => $ability->activation(),
            'range' => $ability->range(),
            'duration' => $ability->duration(),
            'uses' => $ability->uses(),
            'roll_kind' => $ability->rollKind(),
            'formula' => $scaling['formula'],
            'base_formula' => $scaling['base_formula'],
            'roll_scaling' => $scaling,
            'damage_type' => $ability->damageType(),
            'save_ability' => $ability->saveAbility(),
            'save_dc' => $ability->saveAbility() !== null
                ? $saveDc
                : null,
            'spell_attack' => $ability->isSpellAttack()
                ? $spellAttack
                : null,
            'roll_modifier' => $modifier,
            'target_mode' => (
                $ability->isSpellAttack()
                || in_array(
                    $ability->rollKind(),
                    ['damage', 'healing'],
                    true
                )
            )
                ? 'creature'
                : 'none',
            'default_target_kind' =>
                strtolower($ability->range()) === 'self'
                    ? 'self'
                    : '',
            'learned' => $learned,
            'spell_level' => $ability->spellLevel(),
        ];
    }

    /**
     * Group available Arcane Pantry entries into player-facing shelves.
     *
     * Cantrips are kept distinct from numbered spell levels. Class features
     * remain available on their own shelf rather than being forced into a
     * fictional spell level.
     *
     * @param array<int,array<string,mixed>> $entries
     * @return array<int,array{
     *     key:string,
     *     label:string,
     *     kind:string,
     *     level:?int,
     *     entries:array<int,array<string,mixed>>
     * }>
     */
    private function indexedShelves(array $entries): array
    {
        $cantrips = [];
        $levels = [];
        $features = [];

        foreach ($entries as $entry) {
            $kind = (string) ($entry['kind'] ?? '');
            $spellLevel = max(
                0,
                (int) ($entry['spell_level'] ?? 0)
            );

            if ($kind === 'cantrip') {
                $cantrips[] = $entry;
                continue;
            }

            if ($kind === 'spell') {
                $level = max(1, $spellLevel);
                $levels[$level][] = $entry;
                continue;
            }

            $features[] = $entry;
        }

        ksort($levels);

        $shelves = [];

        if ($cantrips !== []) {
            $shelves[] = [
                'key' => 'cantrips',
                'label' => 'Cantrips',
                'kind' => 'cantrip',
                'level' => null,
                'entries' => $cantrips,
            ];
        }

        foreach ($levels as $level => $spells) {
            $shelves[] = [
                'key' => 'level-' . $level,
                'label' => 'Level ' . $level,
                'kind' => 'spell',
                'level' => $level,
                'entries' => $spells,
            ];
        }

        if ($features !== []) {
            $shelves[] = [
                'key' => 'features',
                'label' => 'Features',
                'kind' => 'feature',
                'level' => null,
                'entries' => $features,
            ];
        }

        return $shelves;
    }

    private function castingAbility(string $class): ?string
    {
        return match ($class) {
            'artificer', 'wizard' => 'intelligence',
            'cleric', 'druid', 'ranger' => 'wisdom',
            'bard', 'paladin', 'sorcerer', 'warlock' => 'charisma',
            default => null,
        };
    }

    /**
     * Highest spell circle currently available to this adventurer.
     */
    private function maximumSpellLevel(
        string $class,
        int $level
    ): int {
        $slots = $this->slots(
            $class,
            $level
        );

        if ($slots === []) {
            return 0;
        }

        return max(
            array_column(
                $slots,
                'level'
            )
        );
    }

    /** @return array<int, array{level:int,total:int}> */
    private function slots(string $class, int $level): array
    {
        if ($level < 1) { return []; }

        if ($class === 'warlock') {
            $pactLevel = match (true) {
                $level >= 9 => 5,
                $level >= 7 => 4,
                $level >= 5 => 3,
                $level >= 3 => 2,
                default => 1,
            };
            $count = $level >= 11 ? 3 : ($level >= 2 ? 2 : 1);
            return [['level' => $pactLevel, 'total' => $count]];
        }

        if (in_array($class, ['bard','cleric','druid','sorcerer','wizard'], true)) {
            return $this->slotRow(self::FULL_CASTER_SLOTS[$level] ?? self::FULL_CASTER_SLOTS[20]);
        }

        if ($class === 'artificer') {
            $casterLevel = max(1, (int) ceil($level / 2));
            return $this->slotRow(self::FULL_CASTER_SLOTS[$casterLevel]);
        }

        if (in_array($class, ['paladin','ranger'], true)) {
            if ($level < 2) { return []; }

            /*
             * Single-class half-caster slot progression advances on odd
             * class levels after spellcasting begins. Using floor(level / 2)
             * incorrectly withholds Level 2 slots at class Level 5 and Level
             * 5 slots at class Level 17. Ceil(level / 2) maps the certified
             * Paladin/Ranger table onto the shared full-caster slot rows.
             */
            $casterLevel = max(
                1,
                (int) ceil($level / 2)
            );

            return $this->slotRow(
                self::FULL_CASTER_SLOTS[$casterLevel]
            );
        }

        return [];
    }

    /** @return array<int,array{level:int,total:int}> */
    private function slotRow(array $counts): array
    {
        $slots = [];
        foreach ($counts as $index => $count) {
            if ($count < 1) { continue; }
            $slots[] = ['level' => $index + 1, 'total' => $count];
        }
        return $slots;
    }
}

