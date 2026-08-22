<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate;

use GreatMarketrealmCompanion\Modules\GuildGate\Services\UpdateGuildProfile;

defined('ABSPATH') || exit;

final class GuildProfile
{
    public const ACCOUNT_TYPE_META = 'gmrc_account_type';
    public const PORTRAIT_ATTACHMENT_META = 'gmrc_profile_portrait_attachment_id';

    public static function accountType(int $userId): string
    {
        $type = (string) get_user_meta($userId, self::ACCOUNT_TYPE_META, true);

        if (in_array($type, AccountType::values(), true)) {
            return $type;
        }

        return user_can($userId, 'gmrc_manage_campaigns')
            ? AccountType::DM
            : AccountType::PLAYER;
    }

    public static function portraitAttachmentId(int $userId): int
    {
        return absint(
            get_user_meta($userId, self::PORTRAIT_ATTACHMENT_META, true)
        );
    }

    public static function bio(int $userId): string
    {
        return (string) get_user_meta(
            $userId,
            UpdateGuildProfile::BIO_META,
            true
        );
    }
}
