<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Core\Support\Ulid;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CharacterIdTest extends TestCase
{
    public function testCanGenerateACharacterId(): void
    {
        $id = CharacterId::generate();

        self::assertTrue(
            Ulid::isValid($id->value())
        );
    }

    public function testCanBeCreatedFromAString(): void
    {
        $value = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

        $id = CharacterId::fromString($value);

        self::assertSame(
            $value,
            $id->value()
        );
    }

    public function testCanBeConvertedToAString(): void
    {
        $value = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

        $id = CharacterId::fromString($value);

        self::assertSame(
            $value,
            (string) $id
        );
    }

    public function testEqualCharacterIdsAreEqual(): void
    {
        $value = '01ARZ3NDEKTSV4RRFFQ69G5FAV';

        $first = CharacterId::fromString($value);
        $second = CharacterId::fromString($value);

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentCharacterIdsAreNotEqual(): void
    {
        $first = CharacterId::fromString(
            '01ARZ3NDEKTSV4RRFFQ69G5FAV'
        );

        $second = CharacterId::fromString(
            '01ARZ3NDEKTSV4RRFFQ69G5FAW'
        );

        self::assertFalse(
            $first->equals($second)
        );
    }

    public function testGeneratedCharacterIdsAreDifferent(): void
    {
        $first = CharacterId::generate();
        $second = CharacterId::generate();

        self::assertFalse(
            $first->equals($second)
        );
    }

    public function testRejectsAnInvalidCharacterId(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterId::fromString(
            'not-a-valid-ulid'
        );
    }

    public function testRejectsAnEmptyCharacterId(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterId::fromString('');
    }

    public function testRejectsACharacterIdWithLeadingWhitespace(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterId::fromString(
            ' 01ARZ3NDEKTSV4RRFFQ69G5FAV'
        );
    }

    public function testRejectsACharacterIdWithTrailingWhitespace(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterId::fromString(
            '01ARZ3NDEKTSV4RRFFQ69G5FAV '
        );
    }
}
