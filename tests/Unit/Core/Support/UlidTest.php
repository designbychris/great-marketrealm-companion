<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Core\Support;

use GreatMarketrealmCompanion\Core\Support\Ulid;
use PHPUnit\Framework\TestCase;

final class UlidTest extends TestCase
{
    public function testGeneratesAValidUlid(): void
    {
        $ulid = Ulid::generate();

        self::assertTrue(
            Ulid::isValid($ulid)
        );
    }

    public function testGeneratedUlidUsesCanonicalStringFormat(): void
    {
        $ulid = Ulid::generate();

        self::assertMatchesRegularExpression(
            '/^[0-9A-HJKMNP-TV-Z]{26}$/',
            $ulid
        );
    }

    public function testValidatesAKnownUlid(): void
    {
        self::assertTrue(
            Ulid::isValid(
                '01ARZ3NDEKTSV4RRFFQ69G5FAV'
            )
        );
    }

    public function testRejectsAnInvalidUlid(): void
    {
        self::assertFalse(
            Ulid::isValid(
                'not-a-valid-ulid'
            )
        );
    }

    public function testGeneratedUlidsAreUnique(): void
    {
        $first = Ulid::generate();
        $second = Ulid::generate();

        self::assertNotSame(
            $first,
            $second
        );
    }
}
