<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Core\Invitations;

defined('ABSPATH') || exit;

/**
 * Generates short, human-friendly invite codes for Companion membership flows.
 *
 * The alphabet deliberately omits characters that are commonly confused when
 * a code is read aloud or copied from a screen (I, O, 0 and 1).
 */
final class InviteCodeGenerator
{
    private const ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    public function generate(): string
    {
        $characters = '';
        $last = strlen(self::ALPHABET) - 1;

        for ($index = 0; $index < 8; $index++) {
            $characters .= self::ALPHABET[random_int(0, $last)];
        }

        return substr($characters, 0, 4) . '-' . substr($characters, 4, 4);
    }
}
