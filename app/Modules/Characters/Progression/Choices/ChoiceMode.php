<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Choices;

use InvalidArgumentException;

defined('ABSPATH') || exit;

final class ChoiceMode
{
    public const SINGLE = 'single';
    public const MULTIPLE = 'multiple';
    public const CHOOSE_N = 'choose-n';

    /** @return array<int,string> */
    public static function all(): array
    {
        return [
            self::SINGLE,
            self::MULTIPLE,
            self::CHOOSE_N,
        ];
    }

    public static function validate(string $mode): string
    {
        if (! in_array($mode, self::all(), true)) {
            throw new InvalidArgumentException(
                'Unsupported advancement choice mode.'
            );
        }

        return $mode;
    }
}
