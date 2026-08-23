<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate\Services;

defined('ABSPATH') || exit;

final class GuildGateAudit
{
    /** @param array<string, scalar|null> $context */
    public function record(string $event, array $context = []): void
    {
        if (! defined('WP_DEBUG') || WP_DEBUG !== true) {
            return;
        }

        $payload = ['event' => sanitize_key($event)] + $context;
        $encoded = function_exists('wp_json_encode')
            ? wp_json_encode($payload)
            : json_encode($payload);

        if (is_string($encoded)) {
            error_log('[GMRC Guild Gate] ' . $encoded);
        }
    }
}
