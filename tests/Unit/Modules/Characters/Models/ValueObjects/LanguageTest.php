<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Language;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LanguageTest extends TestCase
{
    #[DataProvider('supportedLanguageProvider')]
    public function testCanBeCreatedFromSupportedLanguage(
        string $input,
        string $expected
    ): void {
        self::assertSame(
            $expected,
            Language::fromString(
                $input
            )->value()
        );
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function supportedLanguageProvider(): array
    {
        return [
            'common' => ['common', 'common'],
            'dwarvish' => ['dwarvish', 'dwarvish'],
            'elvish' => ['elvish', 'elvish'],
            'giant' => ['giant', 'giant'],
            'gnomish' => ['gnomish', 'gnomish'],
            'goblin' => ['goblin', 'goblin'],
            'halfling' => ['halfling', 'halfling'],
            'orc' => ['orc', 'orc'],
            'fructan' => ['fructan', 'fructan'],
            'vegcant' => ['vegcant', 'vegcant'],
            'mycelian' => ['mycelian', 'mycelian'],
            'dairy tongue' => [
                'dairy-tongue',
                'dairy-tongue',
            ],
            'meat speech' => [
                'meat-speech',
                'meat-speech',
            ],
            'shelf script' => [
                'shelf-script',
                'shelf-script',
            ],
        ];
    }

    public function testNormalisesUppercaseInput(): void
    {
        self::assertSame(
            'common',
            Language::fromString(
                'COMMON'
            )->value()
        );
    }

    public function testTrimsWhitespace(): void
    {
        self::assertSame(
            'fructan',
            Language::fromString(
                '  fructan  '
            )->value()
        );
    }

    public function testNormalisesSpacesToHyphens(): void
    {
        self::assertSame(
            'dairy-tongue',
            Language::fromString(
                'Dairy Tongue'
            )->value()
        );
    }

    public function testNormalisesUnderscoresToHyphens(): void
    {
        self::assertSame(
            'shelf-script',
            Language::fromString(
                'shelf_script'
            )->value()
        );
    }

    #[DataProvider('labelProvider')]
    public function testReturnsCorrectDisplayLabel(
        string $language,
        string $expectedLabel
    ): void {
        self::assertSame(
            $expectedLabel,
            Language::fromString(
                $language
            )->label()
        );
    }

    /**
     * @return array<string,array{string,string}>
     */
    public static function labelProvider(): array
    {
        return [
            'common' => ['common', 'Common'],
            'dwarvish' => ['dwarvish', 'Dwarvish'],
            'elvish' => ['elvish', 'Elvish'],
            'giant' => ['giant', 'Giant'],
            'gnomish' => ['gnomish', 'Gnomish'],
            'goblin' => ['goblin', 'Goblin'],
            'halfling' => ['halfling', 'Halfling'],
            'orc' => ['orc', 'Orc'],
            'fructan' => ['fructan', 'Fructan'],
            'vegcant' => ['vegcant', 'Vegcant'],
            'mycelian' => ['mycelian', 'Mycelian'],
            'dairy tongue' => [
                'dairy-tongue',
                'Dairy Tongue',
            ],
            'meat speech' => [
                'meat-speech',
                'Meat Speech',
            ],
            'shelf script' => [
                'shelf-script',
                'Shelf Script',
            ],
        ];
    }

    #[DataProvider('supportedIdentifierProvider')]
    public function testReportsSupportedLanguage(
        string $language
    ): void {
        self::assertTrue(
            Language::supports(
                $language
            )
        );
    }

    /**
     * @return array<string,array{string}>
     */
    public static function supportedIdentifierProvider(): array
    {
        return [
            'common' => ['common'],
            'dwarvish' => ['dwarvish'],
            'elvish' => ['elvish'],
            'giant' => ['giant'],
            'gnomish' => ['gnomish'],
            'goblin' => ['goblin'],
            'halfling' => ['halfling'],
            'orc' => ['orc'],
            'fructan' => ['fructan'],
            'vegcant' => ['vegcant'],
            'mycelian' => ['mycelian'],
            'dairy tongue' => ['dairy-tongue'],
            'meat speech' => ['meat-speech'],
            'shelf script' => ['shelf-script'],
        ];
    }

    public function testSupportsNormalisedInput(): void
    {
        self::assertTrue(
            Language::supports(
                ' Dairy Tongue '
            )
        );

        self::assertTrue(
            Language::supports(
                'shelf_script'
            )
        );
    }

    public function testDoesNotSupportUnknownLanguage(): void
    {
        self::assertFalse(
            Language::supports(
                'sandwich-sign'
            )
        );
    }

    public function testRejectsEmptyLanguage(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'A Character language cannot be empty.'
        );

        Language::fromString('');
    }

    public function testRejectsWhitespaceOnlyLanguage(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Language::fromString('   ');
    }

    public function testRejectsUnsupportedLanguage(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'The Character language "sandwich-sign" is not supported.'
        );

        Language::fromString(
            'Sandwich Sign'
        );
    }

    public function testEqualLanguagesAreEqual(): void
    {
        self::assertTrue(
            Language::fromString(
                'dairy-tongue'
            )->equals(
                Language::fromString(
                    ' Dairy Tongue '
                )
            )
        );
    }

    public function testDifferentLanguagesAreNotEqual(): void
    {
        self::assertFalse(
            Language::fromString(
                'fructan'
            )->equals(
                Language::fromString(
                    'vegcant'
                )
            )
        );
    }

    public function testConvertsToCanonicalString(): void
    {
        self::assertSame(
            'meat-speech',
            (string) Language::fromString(
                'Meat Speech'
            )
        );
    }

    public function testReturnsEverySupportedLanguage(): void
    {
        $languages = Language::all();

        self::assertCount(
            14,
            $languages
        );

        self::assertContainsOnlyInstancesOf(
            Language::class,
            $languages
        );
    }

    public function testAllReturnsCanonicalLanguageOrder(): void
    {
        $values = array_map(
            static fn (
                Language $language
            ): string => $language->value(),
            Language::all()
        );

        self::assertSame(
            [
                'common',
                'dwarvish',
                'elvish',
                'giant',
                'gnomish',
                'goblin',
                'halfling',
                'orc',
                'fructan',
                'vegcant',
                'mycelian',
                'dairy-tongue',
                'meat-speech',
                'shelf-script',
            ],
            $values
        );
    }

    public function testAllLanguagesHaveUniqueIdentifiers(): void
    {
        $values = array_map(
            static fn (
                Language $language
            ): string => $language->value(),
            Language::all()
        );

        self::assertSame(
            $values,
            array_values(
                array_unique($values)
            )
        );
    }

    public function testAllLanguagesHaveNonEmptyLabels(): void
    {
        foreach (Language::all() as $language) {
            self::assertNotSame(
                '',
                $language->label()
            );
        }
    }

    public function testLanguageIsImmutable(): void
    {
        $common = Language::fromString(
            'common'
        );

        $fructan = Language::fromString(
            'fructan'
        );

        self::assertSame(
            'common',
            $common->value()
        );

        self::assertSame(
            'fructan',
            $fructan->value()
        );

        self::assertNotSame(
            $common,
            $fructan
        );
    }
}
