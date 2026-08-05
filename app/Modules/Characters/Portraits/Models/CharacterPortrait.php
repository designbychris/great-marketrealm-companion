<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Portraits\Models;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects\PortraitAttachmentId;
use GreatMarketrealmCompanion\Modules\Characters\Portraits\ValueObjects\PortraitMode;
use InvalidArgumentException;

defined('ABSPATH') || exit;

/**
 * Character Portrait Model.
 *
 * Supports both Guild-generated illustrations and
 * user-supplied WordPress media attachments.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.9.0
 */
final class CharacterPortrait
{
    private function __construct(
        private readonly PortraitMode $mode,
        private readonly ?PortraitRecipe $recipe,
        private readonly ?PortraitAttachmentId $attachmentId
    ) {
        if (
            $mode->isGenerated()
            && ! $recipe instanceof PortraitRecipe
        ) {
            throw new InvalidArgumentException(
                'A generated portrait requires a portrait recipe.'
            );
        }

        if (
            $mode->isCustom()
            && ! $attachmentId
                instanceof PortraitAttachmentId
        ) {
            throw new InvalidArgumentException(
                'A custom portrait requires an attachment identifier.'
            );
        }
    }

    public static function generated(
        PortraitRecipe $recipe
    ): self {
        return new self(
            PortraitMode::generated(),
            $recipe,
            null
        );
    }

    public static function custom(
        PortraitAttachmentId $attachmentId,
        ?PortraitRecipe $fallbackRecipe = null
    ): self {
        return new self(
            PortraitMode::custom(),
            $fallbackRecipe,
            $attachmentId
        );
    }

    public static function none(): self
    {
        return new self(
            PortraitMode::none(),
            null,
            null
        );
    }

    public function mode(): PortraitMode
    {
        return $this->mode;
    }

    public function recipe(): ?PortraitRecipe
    {
        return $this->recipe;
    }

    public function attachmentId(): ?PortraitAttachmentId
    {
        return $this->attachmentId;
    }

    /**
     * Return the generated portrait that can be used when
     * a custom image is removed.
     */
    public function useGeneratedFallback(): self
    {
        if (! $this->recipe instanceof PortraitRecipe) {
            return self::none();
        }

        return self::generated(
            $this->recipe
        );
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'mode' => $this->mode->value(),
            'recipe' => $this->recipe?->toArray(),
            'attachment_id' =>
                $this->attachmentId?->value(),
        ];
    }
}
