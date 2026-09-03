<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Core\Support;

use DateTimeImmutable;
use Throwable;

final class MarketRealmDate
{
    public static function date(string $value): string
    {
        if (trim($value) === '') { return ''; }
        try { $date = new DateTimeImmutable($value); } catch (Throwable) { return $value; }
        $day = (int) $date->format('j');
        return $day . self::ordinal($day) . ' ' . $date->format('F Y');
    }

    public static function dateTime(string $value): string
    {
        if (trim($value) === '') { return ''; }
        try { $date = new DateTimeImmutable($value); } catch (Throwable) { return $value; }
        return self::date($value) . ' · ' . $date->format('H:i');
    }

    private static function ordinal(int $day): string
    {
        if ($day >= 11 && $day <= 13) { return 'th'; }
        return match ($day % 10) { 1 => 'st', 2 => 'nd', 3 => 'rd', default => 'th' };
    }
}
