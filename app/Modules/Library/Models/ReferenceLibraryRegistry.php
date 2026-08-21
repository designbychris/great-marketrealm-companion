<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Library\Models;

use GreatMarketrealmCompanion\Modules\Library\Contracts\ReferenceCatalogueInterface;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Registry for first-class Post-Calling reference catalogues.
 */
final class ReferenceLibraryRegistry
{
    /** @var array<string,ReferenceCatalogueInterface> */
    private array $catalogues = [];

    public function add(
        ReferenceCatalogueInterface $catalogue
    ): void {
        $key = sanitize_key(
            $catalogue->key()
        );

        if ($key === '') {
            throw new InvalidArgumentException(
                'A reference catalogue must have a valid key.'
            );
        }

        if (isset($this->catalogues[$key])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Reference catalogue "%s" is already registered.',
                    $key
                )
            );
        }

        $this->catalogues[$key] = $catalogue;
    }

    public function has(string $key): bool
    {
        return isset(
            $this->catalogues[
                sanitize_key($key)
            ]
        );
    }

    public function get(
        string $key
    ): ?ReferenceCatalogueInterface {
        return $this->catalogues[
            sanitize_key($key)
        ] ?? null;
    }

    /** @return array<int,ReferenceCatalogueInterface> */
    public function all(): array
    {
        return array_values(
            $this->catalogues
        );
    }

    /** @return array<int,array<string,mixed>> */
    public function summaries(): array
    {
        return array_map(
            static fn (
                ReferenceCatalogueInterface $catalogue
            ): array => $catalogue->summary(),
            $this->all()
        );
    }

    public function count(): int
    {
        return count(
            $this->catalogues
        );
    }
}
