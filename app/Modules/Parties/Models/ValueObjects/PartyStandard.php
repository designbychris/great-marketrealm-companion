<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Models\ValueObjects;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Immutable visual identity for a Fellowship.
 *
 * Values are catalogue-backed rather than arbitrary CSS or markup.
 */
final class PartyStandard
{
    public const DEFAULT_PALETTE = 'aubergine-gold';
    public const DEFAULT_EMBLEM = 'guild-star';
    public const DEFAULT_ORNAMENT = 'flourish';

    private const PALETTES = [
        'aubergine-gold',
        'pantry-green',
        'frost-blue',
        'berry-red',
        'cheddar-gold',
    ];

    private const EMBLEMS = [
        'guild-star',
        'market-leaf',
        'company-crown',
        'adventurers-cross',
        'guild-cart',
    ];

    private const ORNAMENTS = [
        'flourish',
        'laurels',
        'stars',
        'diamond',
        'plain',
    ];

    private function __construct(
        private readonly string $palette,
        private readonly string $emblem,
        private readonly string $ornament
    ) {
        if (! in_array($palette, self::PALETTES, true)) {
            throw new InvalidArgumentException(
                'The supplied Fellowship Standard palette is not supported.'
            );
        }

        if (! in_array($emblem, self::EMBLEMS, true)) {
            throw new InvalidArgumentException(
                'The supplied Fellowship Standard emblem is not supported.'
            );
        }

        if (! in_array($ornament, self::ORNAMENTS, true)) {
            throw new InvalidArgumentException(
                'The supplied Fellowship Standard ornament is not supported.'
            );
        }
    }

    public static function default(): self
    {
        return new self(
            self::DEFAULT_PALETTE,
            self::DEFAULT_EMBLEM,
            self::DEFAULT_ORNAMENT
        );
    }

    public static function make(
        string $palette,
        string $emblem,
        string $ornament
    ): self {
        return new self(
            $palette,
            $emblem,
            $ornament
        );
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return self::make(
            (string) ($data['palette'] ?? self::DEFAULT_PALETTE),
            (string) ($data['emblem'] ?? self::DEFAULT_EMBLEM),
            (string) ($data['ornament'] ?? self::DEFAULT_ORNAMENT)
        );
    }

    public function palette(): string
    {
        return $this->palette;
    }

    public function emblem(): string
    {
        return $this->emblem;
    }

    public function ornament(): string
    {
        return $this->ornament;
    }

    /**
     * @return array{palette:string,emblem:string,ornament:string}
     */
    public function toArray(): array
    {
        return [
            'palette' => $this->palette,
            'emblem' => $this->emblem,
            'ornament' => $this->ornament,
        ];
    }

    /**
     * @return string[]
     */
    public static function palettes(): array
    {
        return self::PALETTES;
    }

    /**
     * @return string[]
     */
    public static function emblems(): array
    {
        return self::EMBLEMS;
    }

    /**
     * @return string[]
     */
    public static function ornaments(): array
    {
        return self::ORNAMENTS;
    }

    public function emblemGlyph(): string
    {
        return match ($this->emblem) {
            'market-leaf' => '❧',
            'company-crown' => '♛',
            'adventurers-cross' => '⚔',
            'guild-cart' => '◇',
            default => '✦',
        };
    }

    public function ornamentGlyph(): string
    {
        return match ($this->ornament) {
            'laurels' => '❦',
            'stars' => '✦ ✦ ✦',
            'diamond' => '◆',
            'plain' => '',
            default => '❧',
        };
    }
}
