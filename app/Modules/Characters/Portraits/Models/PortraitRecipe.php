<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Models;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects\PortraitLayerId;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects\PortraitSeed;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Immutable Portrait Recipe.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class PortraitRecipe
{
    /**
     * @param array<string,PortraitLayerId> $layers
     */
    private function __construct(
        private readonly PortraitSeed $seed,
        private readonly array $layers
    ) {
        if ($layers === []) {
            throw new InvalidArgumentException(
                'A generated portrait recipe requires at least one layer.'
            );
        }
    }

    /**
     * @param array<string,string|PortraitLayerId> $layers
     */
    public static function create(
        PortraitSeed $seed,
        array $layers
    ): self {
        $normalised = [];

        foreach ($layers as $slot => $layer) {
            $slot = sanitize_key(
                (string) $slot
            );

            if ($slot === '') {
                continue;
            }

            $normalised[$slot] =
                $layer instanceof PortraitLayerId
                    ? $layer
                    : PortraitLayerId::fromString(
                        (string) $layer
                    );
        }

        return new self(
            $seed,
            $normalised
        );
    }

    public function seed(): PortraitSeed
    {
        return $this->seed;
    }

    public function layer(
        string $slot
    ): ?PortraitLayerId {
        return $this->layers[
            sanitize_key($slot)
        ] ?? null;
    }

    /**
     * @return array<string,PortraitLayerId>
     */
    public function layers(): array
    {
        return $this->layers;
    }

    /**
     * @return array{
     *     seed:string,
     *     layers:array<string,string>
     * }
     */
    public function toArray(): array
    {
        return [
            'seed' => $this->seed->value(),
            'layers' => array_map(
                static fn (
                    PortraitLayerId $layer
                ): string => $layer->value(),
                $this->layers
            ),
        ];
    }

    /**
     * @param array{
     *     seed?:mixed,
     *     layers?:mixed
     * } $data
     */
    public static function fromArray(
        array $data
    ): self {
        $seed = isset($data['seed'])
            && is_scalar($data['seed'])
                ? (string) $data['seed']
                : '';

        $layers = is_array(
            $data['layers'] ?? null
        )
            ? $data['layers']
            : [];

        return self::create(
            PortraitSeed::fromString($seed),
            $layers
        );
    }

    public function equals(
        self $other
    ): bool {
        return $this->toArray()
            === $other->toArray();
    }
}
