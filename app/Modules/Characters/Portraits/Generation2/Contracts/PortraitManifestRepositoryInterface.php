<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Contracts;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Models\PortraitManifest;

defined('ABSPATH') || exit;

interface PortraitManifestRepositoryInterface
{
    /**
     * @return array<int,PortraitManifest>
     */
    public function all(): array;

    public function find(string $manifestId): ?PortraitManifest;
}
