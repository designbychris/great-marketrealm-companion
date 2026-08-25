<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Catalogues;

use GreatMarketrealmCompanion\Modules\Library\Spells\Repositories\CanonicalSpellRegister; // Protected canonical layer.
use GreatMarketrealmCompanion\Modules\Library\Spells\Repositories\SharedSpellRegister;

defined('ABSPATH') || exit;

/**
 * Canonical read-only Spell Register curated for Sage's Spellbook.
 */
final class SpellReferenceCatalogue extends AbstractFoundationCatalogue
{
    public function __construct(
        private ?SharedSpellRegister $register = null
    ) {
        $this->register ??= new SharedSpellRegister();
    }

    public function key(): string
    {
        return 'spells';
    }

    public function label(): string
    {
        return "Sage's Spellbook";
    }

    public function description(): string
    {
        return 'A canonical, searchable register of Marketrealm spells, renamed magic and class access.';
    }

    public function phase(): string
    {
        return 'III.13.1A';
    }

    public function status(): string
    {
        return 'registered';
    }

    /** @return array<int,array<string,mixed>> */
    public function entries(): array
    {
        return array_map(
            static fn ($spell): array => $spell->toArray(),
            $this->register->all()
        );
    }

    /** @return array<string,mixed> */
    public function summary(): array
    {
        $summary = parent::summary();
        $summary['renamed_count'] = count(
            $this->register->byKind('renamed')
        );
        $summary['marketrealm_original_count'] = count(
            $this->register->byKind('marketrealm-original')
        );
        $summary['source_variant_count'] =
            $this->register->sourceVariantCount();
        $summary['source_issue_count'] = count(
            $this->register->withSourceIssues()
        );

        return $summary;
    }
}
