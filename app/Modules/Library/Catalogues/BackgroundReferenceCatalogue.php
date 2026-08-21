<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Catalogues;

use GreatMarketrealmCompanion\Modules\Library\Backgrounds\Repositories\HandbookBackgroundRegister;

defined('ABSPATH') || exit;

/**
 * Canonical seam for expanded adventurer backgrounds.
 */
final class BackgroundReferenceCatalogue extends AbstractFoundationCatalogue
{
    public function __construct(
        private ?HandbookBackgroundRegister $register = null
    ) {
        $this->register ??= new HandbookBackgroundRegister();
    }

    public function key(): string { return 'backgrounds'; }
    public function label(): string { return 'Background Register'; }
    public function description(): string
    {
        return 'The canonical optional Marketrealm backgrounds that feed character inscription from one shared source.';
    }
    public function phase(): string { return 'III.13.3'; }
    public function status(): string { return 'registered'; }
    public function entries(): array
    {
        return array_map(
            static fn ($background): array => $background->toArray(),
            $this->register->all()
        );
    }
}
