<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Characters\Progression\Folios;

use InvalidArgumentException;

defined('ABSPATH') || exit;

final class FolioStatus
{
    public const READY = 'ready';
    public const ATTENTION = 'attention';
    public const INFORMATION = 'information';

    /**
     * @return array<int,string>
     */
    public static function all(): array
    {
        return [
            self::READY,
            self::ATTENTION,
            self::INFORMATION,
        ];
    }

    public static function validate(
        string $status
    ): string {
        if (! in_array(
            $status,
            self::all(),
            true
        )) {
            throw new InvalidArgumentException(
                'Unsupported Rising Folio status.'
            );
        }

        return $status;
    }
}
