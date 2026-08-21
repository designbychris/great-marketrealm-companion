<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Catalogues;

defined('ABSPATH') || exit;

/**
 * Reserved canonical seam for Sage's Spellbook.
 */
final class SpellReferenceCatalogue extends AbstractFoundationCatalogue
{
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
        return 'III.13.1';
    }
}
