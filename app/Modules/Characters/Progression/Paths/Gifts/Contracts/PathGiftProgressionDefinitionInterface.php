<?php
declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Contracts;

defined('ABSPATH') || exit;

interface PathGiftProgressionDefinitionInterface
{
    public function supports(string $pathKey): bool;
    public function pathKey(): string;
    public function pathLabel(): string;

    /** @return array<int,array<string,mixed>> */
    public function gifts(): array;
}
