<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Core\Support;

use Symfony\Component\Uid\Ulid as SymfonyUlid;

defined('ABSPATH') || exit;

/**
 * Generates and validates ULID identifiers.
 *
 * This class provides a framework-level boundary around Symfony's
 * UID component so that the rest of GMRC does not depend directly
 * on its underlying implementation.
 *
 * @package GreatMarketrealmCompanion
 * @since 0.5.0
 */
final class Ulid
{
    /**
     * Generate a new ULID.
     */
    public static function generate(): string
    {
        return (string) new SymfonyUlid();
    }

    /**
     * Determine whether a value is a valid ULID.
     */
    public static function isValid(string $value): bool
    {
        return SymfonyUlid::isValid($value);
    }
}
