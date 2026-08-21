<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Contracts;

defined('ABSPATH') || exit;

/**
 * Shared read-only contract for Post-Calling reference catalogues.
 */
interface ReferenceCatalogueInterface
{
    public function key(): string;

    public function label(): string;

    public function description(): string;

    public function canonicalSource(): string;

    public function phase(): string;

    public function status(): string;

    /** @return array<int,array<string,mixed>> */
    public function entries(): array;

    /** @return array<string,mixed> */
    public function summary(): array;
}
