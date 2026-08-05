<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Rendering;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;

defined('ABSPATH') || exit;

/**
 * Portrait Render Context.
 *
 * Contains the presentation values required by the procedural
 * Guild Illuminator while remaining independent of HTML views.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class PortraitRenderContext
{
    /**
     * @param array<string,string> $layers
     */
    private function __construct(
        private readonly string $name,
        private readonly string $race,
        private readonly string $characterClass,
        private readonly array $layers,
        private readonly string $seed,
        private readonly int $backgroundVariant,
        private readonly int $bodyVariant,
        private readonly int $outfitVariant,
        private readonly int $equipmentVariant,
        private readonly int $effectsVariant,
    ) {
    }

    /**
     * Build a context from a persisted portrait.
     */
    public static function fromViewModel(
        PortraitViewModel $portrait
    ): self {
        return new self(
            name: $portrait->name(),
            race: $portrait->race(),
            characterClass:
                $portrait->characterClass(),
            layers: $portrait->layers(),
            seed: $portrait->seed() ?? '',
            backgroundVariant:
                $portrait->variant(
                    'background',
                    3
                ),
            bodyVariant:
                $portrait->variant(
                    'body',
                    3
                ),
            outfitVariant:
                $portrait->variant(
                    'outfit',
                    3
                ),
            equipmentVariant:
                $portrait->variant(
                    'equipment',
                    3
                ),
            effectsVariant:
                $portrait->variant(
                    'effects',
                    3
                ),
        );
    }

    /**
     * Build a provisional context for the Character Creator.
     */
    public static function provisional(
        string $name = '',
        string $race = '',
        string $characterClass = ''
    ): self {
        return new self(
            name: trim($name),
            race: sanitize_key($race),
            characterClass:
                sanitize_key($characterClass),
            layers: [],
            seed: '',
            backgroundVariant: 1,
            bodyVariant: 1,
            outfitVariant: 1,
            equipmentVariant: 1,
            effectsVariant: 1,
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function race(): string
    {
        return $this->race;
    }

    public function characterClass(): string
    {
        return $this->characterClass;
    }

    public function seed(): string
    {
        return $this->seed;
    }

    public function layer(
        string $slot
    ): string {
        $slot = sanitize_key($slot);

        $value = $this->layers[$slot]
            ?? '';

        return is_string($value)
            ? $value
            : '';
    }

    public function backgroundVariant(): int
    {
        return $this->backgroundVariant;
    }

    public function bodyVariant(): int
    {
        return $this->bodyVariant;
    }

    public function outfitVariant(): int
    {
        return $this->outfitVariant;
    }

    public function equipmentVariant(): int
    {
        return $this->equipmentVariant;
    }

    public function effectsVariant(): int
    {
        return $this->effectsVariant;
    }

    public function displayName(): string
    {
        return $this->name !== ''
            ? $this->name
            : 'Awaiting Subject';
    }

    public function initial(): string
    {
        if ($this->name === '') {
            return '?';
        }

        $initial = function_exists(
            'mb_substr'
        )
            ? mb_substr(
                $this->name,
                0,
                1
            )
            : substr(
                $this->name,
                0,
                1
            );

        return function_exists(
            'mb_strtoupper'
        )
            ? mb_strtoupper($initial)
            : strtoupper($initial);
    }

    /**
     * Return a stable identifier used by SVG definitions.
     */
    public function uniqueId(): string
    {
        return substr(
            hash(
                'sha256',
                implode(
                    '|',
                    [
                        $this->seed,
                        $this->name,
                        $this->race,
                        $this->characterClass,
                    ]
                )
            ),
            0,
            10
        );
    }

    public function definitionId(
        string $name
    ): string {
        return 'gmrc-portrait-'
            . sanitize_key($name)
            . '-'
            . $this->uniqueId();
    }

    /**
     * @return array<int,string>
     */
    public function backgroundColours(): array
    {
        $palettes = [
            1 => [
                '#fff4ce',
                '#e5c884',
                '#a77a3c',
            ],
            2 => [
                '#eef1d2',
                '#b9c48d',
                '#68774b',
            ],
            3 => [
                '#eee1f1',
                '#b48cb8',
                '#604368',
            ],
        ];

        return $palettes[
            $this->backgroundVariant
        ];
    }

    /**
     * @return array<int,string>
     */
    public function bodyColours(): array
    {
        $palettes = [
            1 => [
                '#705078',
                '#392240',
            ],
            2 => [
                '#77895d',
                '#38482f',
            ],
            3 => [
                '#a25f4e',
                '#593125',
            ],
        ];

        return $palettes[
            $this->bodyVariant
        ];
    }

    /**
     * @return array<int,string>
     */
    public function outfitColours(): array
    {
        $palettes = [
            1 => [
                '#9d5162',
                '#5c2433',
            ],
            2 => [
                '#687f50',
                '#344329',
            ],
            3 => [
                '#596f94',
                '#2e3d5a',
            ],
        ];

        return $palettes[
            $this->outfitVariant
        ];
    }
}
