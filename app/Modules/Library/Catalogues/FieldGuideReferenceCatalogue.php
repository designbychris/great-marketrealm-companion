<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Catalogues;

use GreatMarketrealmCompanion\Modules\Library\FieldGuide\Services\GuildFieldGuide;

defined('ABSPATH') || exit;

final class FieldGuideReferenceCatalogue extends AbstractFoundationCatalogue
{
    public function __construct(private GuildFieldGuide $guide) {}

    public function key(): string
    {
        return 'field-guide';
    }

    public function label(): string
    {
        return 'The Guild Field Guide';
    }

    public function description(): string
    {
        return 'Illustrated, Steward-approved creature lore for adventurers of the Guild.';
    }

    public function canonicalSource(): string
    {
        return 'The Great Marketrealm Dungeon Master Guide';
    }

    public function phase(): string
    {
        return 'III.16.7';
    }

    public function status(): string
    {
        return 'registered';
    }

    public function entries(): array
    {
        return $this->guide->all();
    }
}
