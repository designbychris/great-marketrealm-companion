<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate;

defined('ABSPATH') || exit;

final class GuildProfile
{
    public const ACCOUNT_TYPE_META = 'gmrc_account_type';
    public const PORTRAIT_ATTACHMENT_META = 'gmrc_profile_portrait_attachment_id';

    public static function accountType(int $userId): string
    {
        $type = (string) get_user_meta(
            $userId,
            self::ACCOUNT_TYPE_META,
            true
        );

        return in_array($type, AccountType::values(), true)
            ? $type
            : AccountType::PLAYER;
    }
}
