<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CharacterNameTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $name = CharacterName::fromString(
            'Sir Allium'
        );

        self::assertSame(
            'Sir Allium',
            $name->value()
        );
    }

    public function testCanBeConvertedToString(): void
    {
        $name = CharacterName::fromString(
            'Sir Allium'
        );

        self::assertSame(
            'Sir Allium',
            (string) $name
        );
    }

    public function testEqualNamesAreEqual(): void
    {
        $first = CharacterName::fromString(
            'Sir Allium'
        );

        $second = CharacterName::fromString(
            'Sir Allium'
        );

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentNamesAreNotEqual(): void
    {
        $first = CharacterName::fromString(
            'Sir Allium'
        );

        $second = CharacterName::fromString(
            'Captain Carrot'
        );

        self::assertFalse(
            $first->equals($second)
        );
    }

    public function testRejectsEmptyNames(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterName::fromString('');
    }

    public function testRejectsLeadingWhitespace(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterName::fromString(
            ' Sir Allium'
        );
    }

    public function testRejectsTrailingWhitespace(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterName::fromString(
            'Sir Allium '
        );
    }

    public function testRejectsNamesThatAreTooShort(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterName::fromString('A');
    }

    public function testRejectsNamesThatAreTooLong(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterName::fromString(
            str_repeat('A', 81)
        );
    }

    public function testRejectsControlCharacters(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        CharacterName::fromString(
            "Sir\nAllium"
        );
    }
}
