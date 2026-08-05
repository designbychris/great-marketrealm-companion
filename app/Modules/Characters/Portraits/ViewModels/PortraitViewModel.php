<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels;

defined('ABSPATH') || exit;

/**
 * Portrait View Model.
 *
 * Contains presentation-ready portrait information without
 * exposing persistence or rendering services to views.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class PortraitViewModel
{
    /**
     * @param array<string,string> $layers
     */
    public function __construct(
        private readonly string $mode,
        private readonly string $name,
        private readonly string $race,
        private readonly string $raceLabel,
        private readonly string $characterClass,
        private readonly string $classLabel,
        private readonly string $svg = '',
        private readonly array $layers = [],
        private readonly ?string $seed = null,
        private readonly ?int $attachmentId = null,
        private readonly ?string $attachmentUrl = null
    ) {
    }

    public function mode(): string
    {
        return $this->mode;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function race(): string
    {
        return $this->race;
    }

    public function raceLabel(): string
    {
        return $this->raceLabel;
    }

    public function characterClass(): string
    {
        return $this->characterClass;
    }

    public function classLabel(): string
    {
        return $this->classLabel;
    }

    public function svg(): string
    {
        return $this->svg;
    }

    /**
     * @return array<string,string>
     */
    public function layers(): array
    {
        return $this->layers;
    }

    public function layer(
        string $slot
    ): ?string {
        $slot = sanitize_key($slot);

        return $this->layers[$slot] ?? null;
    }

    public function seed(): ?string
    {
        return $this->seed;
    }

    public function attachmentId(): ?int
    {
        return $this->attachmentId;
    }

    public function attachmentUrl(): ?string
    {
        return $this->attachmentUrl;
    }

    public function isGenerated(): bool
    {
        return $this->mode === 'generated';
    }

    public function isCustom(): bool
    {
        return $this->mode === 'custom'
            && $this->attachmentUrl !== null;
    }

    /**
     * Return a deterministic visual variant for a layer.
     */
    public function variant(
        string $slot,
        int $quantity = 3
    ): int {
        $quantity = max(
            1,
            $quantity
        );

        $layer = $this->layer($slot)
            ?? $this->seed
            ?? $slot;

        $hash = hash(
            'crc32b',
            $slot . '|' . $layer
        );

        return (
            (int) hexdec(
                substr(
                    $hash,
                    0,
                    8
                )
            ) % $quantity
        ) + 1;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'mode' => $this->mode,
            'name' => $this->name,
            'race' => $this->race,
            'race_label' => $this->raceLabel,
            'class' => $this->characterClass,
            'class_label' => $this->classLabel,
            'svg' => $this->svg,
            'layers' => $this->layers,
            'seed' => $this->seed,
            'attachment_id' => $this->attachmentId,
            'attachment_url' => $this->attachmentUrl,
        ];
    }
}
