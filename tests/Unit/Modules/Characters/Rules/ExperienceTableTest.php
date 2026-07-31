<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Rules;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Level;
use GreatMarketrealmCompanion\Modules\Characters\Rules\ExperienceTable;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ExperienceTableTest extends TestCase
{
    #[DataProvider('requiredExperienceProvider')]
    public function testReturnsTheRequiredExperienceForEachLevel(
        int $level,
        int $requiredExperience
    ): void {
        self::assertSame(
            $requiredExperience,
            ExperienceTable::requiredFor(
                Level::fromInt($level)
            )
        );
    }

    #[DataProvider('levelLookupProvider')]
    public function testReturnsTheCorrectLevelForExperience(
        int $experience,
        int $expectedLevel
    ): void {
        $level = ExperienceTable::levelForExperience(
            $experience
        );

        self::assertSame(
            $expectedLevel,
            $level->value()
        );
    }

    #[DataProvider('nextLevelProvider')]
    public function testReturnsTheRequiredExperienceForTheNextLevel(
        int $currentLevel,
        int $expectedExperience
    ): void {
        self::assertSame(
            $expectedExperience,
            ExperienceTable::requiredForNext(
                Level::fromInt($currentLevel)
            )
        );
    }

    public function testReturnsNullWhenAlreadyAtMaximumLevel(): void
    {
        self::assertNull(
            ExperienceTable::requiredForNext(
                Level::fromInt(20)
            )
        );
    }

    public function testReturnsTheMaximumLevel(): void
    {
        self::assertSame(
            20,
            ExperienceTable::maximumLevel()->value()
        );
    }

    public function testRejectsNegativeExperience(): void
    {
        $this->expectException(
            InvalidArgumentException::class
        );

        ExperienceTable::levelForExperience(-1);
    }

    /**
     * @return array<string, array{int,int}>
     */
    public static function requiredExperienceProvider(): array
    {
        return [
            'Level 1'  => [1, 0],
            'Level 2'  => [2, 300],
            'Level 3'  => [3, 900],
            'Level 4'  => [4, 2700],
            'Level 5'  => [5, 6500],
            'Level 6'  => [6, 14000],
            'Level 7'  => [7, 23000],
            'Level 8'  => [8, 34000],
            'Level 9'  => [9, 48000],
            'Level 10' => [10, 64000],
            'Level 11' => [11, 85000],
            'Level 12' => [12, 100000],
            'Level 13' => [13, 120000],
            'Level 14' => [14, 140000],
            'Level 15' => [15, 165000],
            'Level 16' => [16, 195000],
            'Level 17' => [17, 225000],
            'Level 18' => [18, 265000],
            'Level 19' => [19, 305000],
            'Level 20' => [20, 355000],
        ];
    }

    /**
     * @return array<string, array{int,int}>
     */
    public static function levelLookupProvider(): array
    {
        return [
            '0 XP'          => [0, 1],
            '299 XP'        => [299, 1],
            '300 XP'        => [300, 2],
            '899 XP'        => [899, 2],
            '900 XP'        => [900, 3],
            '2699 XP'       => [2699, 3],
            '2700 XP'       => [2700, 4],
            '6499 XP'       => [6499, 4],
            '6500 XP'       => [6500, 5],
            '13999 XP'      => [13999, 5],
            '14000 XP'      => [14000, 6],
            '22999 XP'      => [22999, 6],
            '23000 XP'      => [23000, 7],
            '33999 XP'      => [33999, 7],
            '34000 XP'      => [34000, 8],
            '47999 XP'      => [47999, 8],
            '48000 XP'      => [48000, 9],
            '63999 XP'      => [63999, 9],
            '64000 XP'      => [64000, 10],
            '84999 XP'      => [84999, 10],
            '85000 XP'      => [85000, 11],
            '99999 XP'      => [99999, 11],
            '100000 XP'     => [100000, 12],
            '119999 XP'     => [119999, 12],
            '120000 XP'     => [120000, 13],
            '139999 XP'     => [139999, 13],
            '140000 XP'     => [140000, 14],
            '164999 XP'     => [164999, 14],
            '165000 XP'     => [165000, 15],
            '194999 XP'     => [194999, 15],
            '195000 XP'     => [195000, 16],
            '224999 XP'     => [224999, 16],
            '225000 XP'     => [225000, 17],
            '264999 XP'     => [264999, 17],
            '265000 XP'     => [265000, 18],
            '304999 XP'     => [304999, 18],
            '305000 XP'     => [305000, 19],
            '354999 XP'     => [354999, 19],
            '355000 XP'     => [355000, 20],
            '999999 XP'     => [999999, 20],
        ];
    }

    /**
     * @return array<string, array{int,int}>
     */
    public static function nextLevelProvider(): array
    {
        return [
            '1 -> 2'   => [1, 300],
            '2 -> 3'   => [2, 900],
            '3 -> 4'   => [3, 2700],
            '4 -> 5'   => [4, 6500],
            '5 -> 6'   => [5, 14000],
            '6 -> 7'   => [6, 23000],
            '7 -> 8'   => [7, 34000],
            '8 -> 9'   => [8, 48000],
            '9 -> 10'  => [9, 64000],
            '10 -> 11' => [10, 85000],
            '11 -> 12' => [11, 100000],
            '12 -> 13' => [12, 120000],
            '13 -> 14' => [13, 140000],
            '14 -> 15' => [14, 165000],
            '15 -> 16' => [15, 195000],
            '16 -> 17' => [16, 225000],
            '17 -> 18' => [17, 265000],
            '18 -> 19' => [18, 305000],
            '19 -> 20' => [19, 355000],
        ];
    }
}
