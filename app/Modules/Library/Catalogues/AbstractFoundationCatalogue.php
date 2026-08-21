<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Catalogues;

use GreatMarketrealmCompanion\Modules\Library\Contracts\ReferenceCatalogueInterface;

defined('ABSPATH') || exit;

/**
 * Foundation catalogue that reserves a canonical library seam without
 * importing records before its dedicated III.13.x slice.
 */
abstract class AbstractFoundationCatalogue implements ReferenceCatalogueInterface
{
    public function canonicalSource(): string
    {
        return 'The Great Marketrealm - Players Handbook';
    }

    public function status(): string
    {
        return 'foundation';
    }

    /** @return array<int,array<string,mixed>> */
    public function entries(): array
    {
        return [];
    }

    /** @return array<string,mixed> */
    public function summary(): array
    {
        return [
            'key' => $this->key(),
            'label' => $this->label(),
            'description' => $this->description(),
            'canonical_source' => $this->canonicalSource(),
            'phase' => $this->phase(),
            'status' => $this->status(),
            'entry_count' => count($this->entries()),
        ];
    }
}
