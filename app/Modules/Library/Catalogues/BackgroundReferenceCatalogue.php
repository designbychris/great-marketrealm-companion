<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Catalogues;

defined('ABSPATH') || exit;

/**
 * Reserved canonical seam for expanded adventurer backgrounds.
 */
final class BackgroundReferenceCatalogue extends AbstractFoundationCatalogue
{
    public function key(): string
    {
        return 'backgrounds';
    }

    public function label(): string
    {
        return 'Background Register';
    }

    public function description(): string
    {
        return 'The canonical background options that will feed character inscription from one shared source.';
    }

    public function phase(): string
    {
        return 'III.13.3';
    }
}
