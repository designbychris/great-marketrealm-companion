<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Models\ValueObjects;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Language;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Languages;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class LanguagesTest extends TestCase
{
    public function testCreatesAnEmptyCollection(): void
    {
        $languages = Languages::none();

        self::assertSame(
            [],
            $languages->all()
        );

        self::assertSame(
            [],
            $languages->values()
        );

        self::assertTrue(
            $languages->isEmpty()
        );

        self::assertSame(
            0,
            $languages->count()
        );
    }

    public function testCreatesCollectionFromLanguageValues(): void
    {
        $languages = Languages::fromLanguages(
            Language::fromString('common'),
            Language::fromString('fructan')
        );

        self::assertSame(
            [
                'common',
                'fructan',
            ],
            $languages->values()
        );

        self::assertContainsOnlyInstancesOf(
            Language::class,
            $languages->all()
        );
    }

    public function testCreatesCollectionFromStringIdentifiers(): void
    {
        self::assertSame(
            [
                'common',
                'vegcant',
            ],
            Languages::fromStrings([
                'common',
                'vegcant',
            ])->values()
        );
    }

    public function testNormalisesStringIdentifiers(): void
    {
        self::assertSame(
            [
                'dairy-tongue',
                'shelf-script',
            ],
            Languages::fromStrings([
                ' Dairy Tongue ',
                'SHELF_SCRIPT',
            ])->values()
        );
    }

    public function testRemovesDuplicateLanguages(): void
    {
        $languages = Languages::fromStrings([
            'common',
            'COMMON',
            ' common ',
        ]);

        self::assertSame(
            ['common'],
            $languages->values()
        );

        self::assertSame(
            1,
            $languages->count()
        );
    }

    public function testReturnsLanguagesInCanonicalOrder(): void
    {
        $languages = Languages::fromStrings([
            'shelf-script',
            'common',
            'meat-speech',
            'fructan',
        ]);

        self::assertSame(
            [
                'common',
                'fructan',
                'meat-speech',
                'shelf-script',
            ],
            $languages->values()
        );
    }

    public function testDeterminesWhetherLanguageExistsUsingString(): void
    {
        $languages = Languages::fromStrings([
            'common',
            'fructan',
        ]);

        self::assertTrue(
            $languages->has(
                'common'
            )
        );

        self::assertFalse(
            $languages->has(
                'vegcant'
            )
        );
    }

    public function testDeterminesWhetherLanguageExistsUsingValueObject(): void
    {
        $languages = Languages::fromStrings([
            'mycelian',
        ]);

        self::assertTrue(
            $languages->has(
                Language::fromString(
                    'mycelian'
                )
            )
        );

        self::assertFalse(
            $languages->has(
                Language::fromString(
                    'common'
                )
            )
        );
    }

    public function testLanguageLookupNormalisesInput(): void
    {
        self::assertTrue(
            Languages::fromStrings([
                'dairy-tongue',
            ])->has(
                ' Dairy Tongue '
            )
        );
    }

    public function testAddsLanguageImmutablyUsingString(): void
    {
        $original = Languages::fromStrings([
            'common',
        ]);

        $updated = $original->add(
            'fructan'
        );

        self::assertSame(
            ['common'],
            $original->values()
        );

        self::assertSame(
            [
                'common',
                'fructan',
            ],
            $updated->values()
        );

        self::assertNotSame(
            $original,
            $updated
        );
    }

    public function testAddsLanguageImmutablyUsingValueObject(): void
    {
        $original = Languages::none();

        $updated = $original->add(
            Language::fromString(
                'vegcant'
            )
        );

        self::assertTrue(
            $original->isEmpty()
        );

        self::assertSame(
            ['vegcant'],
            $updated->values()
        );
    }

    public function testAddingExistingLanguageReturnsSameCollection(): void
    {
        $languages = Languages::fromStrings([
            'common',
        ]);

        self::assertSame(
            $languages,
            $languages->add(
                'COMMON'
            )
        );
    }

    public function testAddedLanguagesRemainInCanonicalOrder(): void
    {
        $languages = Languages::fromStrings([
            'shelf-script',
            'meat-speech',
        ]);

        $updated = $languages->add(
            'common'
        );

        self::assertSame(
            [
                'common',
                'meat-speech',
                'shelf-script',
            ],
            $updated->values()
        );
    }

    public function testRemovesLanguageImmutablyUsingString(): void
    {
        $original = Languages::fromStrings([
            'common',
            'fructan',
        ]);

        $updated = $original->remove(
            'common'
        );

        self::assertSame(
            [
                'common',
                'fructan',
            ],
            $original->values()
        );

        self::assertSame(
            ['fructan'],
            $updated->values()
        );

        self::assertNotSame(
            $original,
            $updated
        );
    }

    public function testRemovesLanguageUsingValueObject(): void
    {
        $updated = Languages::fromStrings([
            'common',
            'vegcant',
        ])->remove(
            Language::fromString(
                'vegcant'
            )
        );

        self::assertSame(
            ['common'],
            $updated->values()
        );
    }

    public function testRemovingMissingLanguageReturnsSameCollection(): void
    {
        $languages = Languages::fromStrings([
            'common',
        ]);

        self::assertSame(
            $languages,
            $languages->remove(
                'fructan'
            )
        );
    }

    public function testRemovingLastLanguageCreatesEmptyCollection(): void
    {
        $languages = Languages::fromStrings([
            'common',
        ])->remove(
            'common'
        );

        self::assertTrue(
            $languages->isEmpty()
        );

        self::assertSame(
            0,
            $languages->count()
        );
    }

    public function testMergesLanguageCollections(): void
    {
        $first = Languages::fromStrings([
            'common',
            'fructan',
        ]);

        $second = Languages::fromStrings([
            'vegcant',
            'shelf-script',
        ]);

        self::assertSame(
            [
                'common',
                'fructan',
                'vegcant',
                'shelf-script',
            ],
            $first->merge(
                $second
            )->values()
        );
    }

    public function testMergeRemovesDuplicates(): void
    {
        $first = Languages::fromStrings([
            'common',
            'fructan',
        ]);

        $second = Languages::fromStrings([
            'common',
            'vegcant',
        ]);

        self::assertSame(
            [
                'common',
                'fructan',
                'vegcant',
            ],
            $first->merge(
                $second
            )->values()
        );
    }

    public function testMergeDoesNotMutateSources(): void
    {
        $first = Languages::fromStrings([
            'common',
        ]);

        $second = Languages::fromStrings([
            'fructan',
        ]);

        $first->merge($second);

        self::assertSame(
            ['common'],
            $first->values()
        );

        self::assertSame(
            ['fructan'],
            $second->values()
        );
    }

    public function testMergingWithEmptyCollectionPreservesValues(): void
    {
        $languages = Languages::fromStrings([
            'common',
            'fructan',
        ]);

        self::assertTrue(
            $languages->equals(
                $languages->merge(
                    Languages::none()
                )
            )
        );
    }

    public function testCountsLanguages(): void
    {
        self::assertSame(
            3,
            Languages::fromStrings([
                'common',
                'fructan',
                'vegcant',
            ])->count()
        );
    }

    public function testEqualCollectionsAreEqual(): void
    {
        $first = Languages::fromStrings([
            'common',
            'fructan',
        ]);

        $second = Languages::fromStrings([
            'fructan',
            'common',
        ]);

        self::assertTrue(
            $first->equals($second)
        );
    }

    public function testDifferentCollectionsAreNotEqual(): void
    {
        self::assertFalse(
            Languages::fromStrings([
                'common',
            ])->equals(
                Languages::fromStrings([
                    'fructan',
                ])
            )
        );
    }

    public function testEmptyCollectionsAreEqual(): void
    {
        self::assertTrue(
            Languages::none()->equals(
                Languages::none()
            )
        );
    }

    public function testRejectsNonStringIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        $this->expectExceptionMessage(
            'Character language identifiers must be strings.'
        );

        Languages::fromStrings([
            123,
        ]);
    }

    public function testRejectsUnsupportedLanguageIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Languages::fromStrings([
            'sandwich-sign',
        ]);
    }

    public function testHasRejectsUnsupportedLanguageIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Languages::none()->has(
            'sandwich-sign'
        );
    }

    public function testAddRejectsUnsupportedLanguageIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Languages::none()->add(
            'sandwich-sign'
        );
    }

    public function testRemoveRejectsUnsupportedLanguageIdentifier(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        Languages::none()->remove(
            'sandwich-sign'
        );
    }

    public function testCollectionIsImmutable(): void
    {
        $original = Languages::fromStrings([
            'common',
        ]);

        $withFructan = $original->add(
            'fructan'
        );

        $withoutCommon = $withFructan->remove(
            'common'
        );

        self::assertSame(
            ['common'],
            $original->values()
        );

        self::assertSame(
            [
                'common',
                'fructan',
            ],
            $withFructan->values()
        );

        self::assertSame(
            ['fructan'],
            $withoutCommon->values()
        );
    }
}
