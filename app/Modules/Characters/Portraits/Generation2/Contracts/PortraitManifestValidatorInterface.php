<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Generation2\Contracts;

defined('ABSPATH') || exit;

interface PortraitManifestValidatorInterface
{
    /**
     * @param array<string,mixed> $manifest
     * @return array<int,string>
     */
    public function validate(array $manifest, string $directory): array;
}
