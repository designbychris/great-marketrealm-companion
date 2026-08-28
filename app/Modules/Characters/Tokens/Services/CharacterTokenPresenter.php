<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Tokens\Services;

use GreatMarketrealmCompanion\Modules\Characters\Portraits\ViewModels\PortraitViewModel;
use GreatMarketrealmCompanion\Modules\Characters\Tokens\Models\CharacterToken;

defined('ABSPATH') || exit;

/**
 * Builds the stable, consumer-friendly Tabletop token projection.
 *
 * This is intentionally suitable for the future Tabletop bridge: the VTT
 * does not need to understand Character portrait persistence internals.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.3.1
 */
final class CharacterTokenPresenter
{
    /** @return array<string,mixed> */
    public function present(CharacterToken $token, PortraitViewModel $portrait): array
    {
        $customUrl = null;

        if ($token->isCustom() && ($token->attachmentId() ?? 0) > 0) {
            $url = wp_get_attachment_image_url((int) $token->attachmentId(), 'full');
            $customUrl = is_string($url) && $url !== '' ? $url : null;
        }

        $source = $token->source();

        // Deleted/unavailable custom media gracefully falls back to the portrait.
        if ($source === CharacterToken::SOURCE_CUSTOM && $customUrl === null) {
            $source = CharacterToken::SOURCE_PORTRAIT;
        }

        return [
            'source' => $source,
            'is_custom' => $source === CharacterToken::SOURCE_CUSTOM,
            'custom_url' => $customUrl,
            'portrait_mode' => $portrait->mode(),
            'portrait_url' => $portrait->attachmentUrl(),
            'portrait_svg' => $portrait->svg(),
            'frame' => $token->frame(),
            'focus_x' => $token->focusX(),
            'focus_y' => $token->focusY(),
            'zoom' => $token->zoom(),
            'frames' => [
                'guild-brass' => 'Guild Brass',
                'market-oak' => 'Market Oak',
                'leafbound' => 'Leafbound',
                'plain' => 'Plain Ring',
            ],
        ];
    }
}
