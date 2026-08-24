<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Arcana\Services;

use GreatMarketrealmCompanion\Modules\Characters\Arcana\Models\ArcaneAbilityDefinition;
use GreatMarketrealmCompanion\Modules\Library\Spells\Repositories\CanonicalSpellRegister;

defined('ABSPATH') || exit;

/**
 * Compatibility bridge from stable character spell IDs to Sage's canonical
 * Spell Register. Only explicit, source-supported aliases are mapped.
 */
final class CanonicalSpellReferenceResolver
{
    /** @var array<string,string> */
    private const LEGACY_ALIASES = [
        'restorative-preserve' => 'cure-meats',
        'market-missile' => 'mystery-mustard-missile',
        'aisle-lightning' => 'lightning-lemonade',
        'stockroom-fireball' => 'flame-grilled-fireball',
    ];

    public function __construct(
        private ?CanonicalSpellRegister $register = null
    ) {
        $this->register ??= new CanonicalSpellRegister();
    }

    /** @return array<string,mixed> */
    public function resolve(
        ArcaneAbilityDefinition $ability
    ): array {
        if (! in_array($ability->kind(), ['spell', 'cantrip'], true)) {
            return $this->fallback($ability);
        }

        $canonicalKey = self::LEGACY_ALIASES[$ability->id()]
            ?? (
                $this->register->find($ability->id()) !== null
                    ? $ability->id()
                    : null
            );

        if ($canonicalKey === null) {
            return $this->fallback($ability);
        }

        $spell = $this->register->find($canonicalKey);

        if ($spell === null) {
            return $this->fallback($ability);
        }

        $variants = $spell->variants();
        $sourceText = trim(
            (string) (
                $variants[0]['source_text']
                ?? ''
            )
        );

        return [
            'status' => 'canonical',
            'stable_id' => $ability->id(),
            'canonical_key' => $spell->key(),
            'label' => $spell->name(),
            'legacy_label' => $ability->label(),
            'detail' => $sourceText !== ''
                ? $sourceText
                : $ability->description(),
            'original_spell' => $spell->originalSpell(),
            'source_issues' => $spell->sourceIssues(),
            'variant_count' => count($variants),
        ];
    }

    /** @return array<string,mixed> */
    private function fallback(
        ArcaneAbilityDefinition $ability
    ): array {
        return [
            'status' => 'unmatched',
            'stable_id' => $ability->id(),
            'canonical_key' => null,
            'label' => $ability->label(),
            'legacy_label' => $ability->label(),
            'detail' => $ability->description(),
            'original_spell' => null,
            'source_issues' => [],
            'variant_count' => 0,
        ];
    }
}
