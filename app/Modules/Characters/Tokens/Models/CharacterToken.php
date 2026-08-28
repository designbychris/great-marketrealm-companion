<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Tokens\Models;

use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Tabletop token preferences belonging to one Companion Character.
 *
 * The token deliberately remains separate from the Character portrait.
 * A dedicated uploaded token may be used without replacing the Ledger art,
 * while the portrait remains the safe default/fallback for Tabletop clients.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.1
 */
final class CharacterToken
{
    public const SOURCE_PORTRAIT = 'portrait';
    public const SOURCE_CUSTOM = 'custom';

    /** @var string[] */
    public const FRAMES = [
        'guild-brass',
        'market-oak',
        'leafbound',
        'plain',
    ];

    private function __construct(
        private readonly string $source,
        private readonly ?int $attachmentId,
        private readonly string $frame,
        private readonly int $focusX,
        private readonly int $focusY,
        private readonly int $zoom
    ) {
        if (! in_array($source, [self::SOURCE_PORTRAIT, self::SOURCE_CUSTOM], true)) {
            throw new InvalidArgumentException('Unknown tabletop token source.');
        }

        if ($source === self::SOURCE_CUSTOM && ($attachmentId ?? 0) < 1) {
            throw new InvalidArgumentException('A custom tabletop token requires an attachment.');
        }

        if (! in_array($frame, self::FRAMES, true)) {
            throw new InvalidArgumentException('Unknown tabletop token frame.');
        }

        if ($focusX < 0 || $focusX > 100 || $focusY < 0 || $focusY > 100) {
            throw new InvalidArgumentException('Tabletop token focus must be between 0 and 100.');
        }

        if ($zoom < 100 || $zoom > 220) {
            throw new InvalidArgumentException('Tabletop token zoom must be between 100 and 220.');
        }
    }

    public static function portrait(
        string $frame = 'guild-brass',
        int $focusX = 50,
        int $focusY = 50,
        int $zoom = 100
    ): self {
        return new self(self::SOURCE_PORTRAIT, null, $frame, $focusX, $focusY, $zoom);
    }

    public static function custom(
        int $attachmentId,
        string $frame = 'guild-brass',
        int $focusX = 50,
        int $focusY = 50,
        int $zoom = 100
    ): self {
        return new self(self::SOURCE_CUSTOM, $attachmentId, $frame, $focusX, $focusY, $zoom);
    }

    /** @param array<string,mixed> $data */
    public static function fromArray(array $data): self
    {
        $source = isset($data['source']) && is_scalar($data['source'])
            ? sanitize_key((string) $data['source'])
            : self::SOURCE_PORTRAIT;

        $attachmentId = isset($data['attachment_id'])
            ? (int) $data['attachment_id']
            : null;

        $frame = isset($data['frame']) && is_scalar($data['frame'])
            ? sanitize_key((string) $data['frame'])
            : 'guild-brass';

        $focusX = isset($data['focus_x']) ? (int) $data['focus_x'] : 50;
        $focusY = isset($data['focus_y']) ? (int) $data['focus_y'] : 50;
        $zoom = isset($data['zoom']) ? (int) $data['zoom'] : 100;

        return $source === self::SOURCE_CUSTOM
            ? self::custom((int) $attachmentId, $frame, $focusX, $focusY, $zoom)
            : self::portrait($frame, $focusX, $focusY, $zoom);
    }

    public function source(): string
    {
        return $this->source;
    }

    public function attachmentId(): ?int
    {
        return $this->attachmentId;
    }

    public function frame(): string
    {
        return $this->frame;
    }

    public function focusX(): int
    {
        return $this->focusX;
    }

    public function focusY(): int
    {
        return $this->focusY;
    }

    public function zoom(): int
    {
        return $this->zoom;
    }

    public function isCustom(): bool
    {
        return $this->source === self::SOURCE_CUSTOM;
    }

    public function withDesign(string $frame, int $focusX, int $focusY, int $zoom): self
    {
        return $this->isCustom()
            ? self::custom((int) $this->attachmentId, $frame, $focusX, $focusY, $zoom)
            : self::portrait($frame, $focusX, $focusY, $zoom);
    }

    public function withCustomAttachment(int $attachmentId): self
    {
        return self::custom(
            $attachmentId,
            $this->frame,
            $this->focusX,
            $this->focusY,
            $this->zoom
        );
    }

    public function usePortrait(): self
    {
        return self::portrait(
            $this->frame,
            $this->focusX,
            $this->focusY,
            $this->zoom
        );
    }

    /** @return array<string,mixed> */
    public function toArray(): array
    {
        return [
            'source' => $this->source,
            'attachment_id' => $this->attachmentId,
            'frame' => $this->frame,
            'focus_x' => $this->focusX,
            'focus_y' => $this->focusY,
            'zoom' => $this->zoom,
        ];
    }
}
