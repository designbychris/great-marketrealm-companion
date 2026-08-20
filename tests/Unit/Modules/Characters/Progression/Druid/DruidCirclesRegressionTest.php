<?php
declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Progression\Druid;

use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Druid\Services\DruidCircleSpellCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Gifts\Models\PathGiftCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Progression\Paths\Services\PathCandidateCatalogue;
use PHPUnit\Framework\TestCase;

final class DruidCirclesRegressionTest extends TestCase
{
    private const CIRCLES=[
        'circle-of-eating-fresh','circle-of-the-groveflame',
        'circle-of-the-deep-soil','circle-of-the-compost',
        'circle-of-curdle','circle-of-the-churn',
    ];

    public function testSixDruidCirclesRemainRegistered(): void
    {
        $paths=(new PathCandidateCatalogue())->forClass(CharacterClass::fromString('druid'));
        self::assertSame(self::CIRCLES,array_column($paths,'key'));
    }

    public function testEveryCircleHasCertifiedTwoSixTenFourteenProgression(): void
    {
        $catalogue=new PathGiftCatalogue();
        foreach(self::CIRCLES as $circle) {
            self::assertTrue($catalogue->supports($circle));
            self::assertSame([2,6,10,14],array_column($catalogue->all($circle),'level'));
        }
    }

    public function testCircleOfCompostPreservesCombinedLevelTwoFeatures(): void
    {
        $gift=(new PathGiftCatalogue())->all('circle-of-the-compost')[0];
        self::assertSame('Rotbound Affinity & Compost Surge',$gift['label']);
        self::assertStringContainsString('1d6 + Wisdom modifier HP',$gift['detail']);
        self::assertStringContainsString('proficiency bonus uses per long rest',$gift['detail']);
    }

    public function testCircleOfCurdlePreservesMinusOneAc(): void
    {
        $gift=(new PathGiftCatalogue())->all('circle-of-curdle')[1];
        self::assertStringContainsString('-1 to AC',$gift['detail']);
    }

    public function testDeepSoilPreservesFixedLivingEarthquakeDc(): void
    {
        $gift=(new PathGiftCatalogue())->all('circle-of-the-deep-soil')[3];
        self::assertStringContainsString('DC 16 Dexterity save',$gift['detail']);
    }

    public function testEatingFreshPreservesOneHpPerRound(): void
    {
        $gift=(new PathGiftCatalogue())->all('circle-of-eating-fresh')[0];
        self::assertStringContainsString('1 HP per round',$gift['detail']);
        self::assertStringContainsString('30 feet for 1 minute once per long rest',$gift['detail']);
    }

    public function testGroveflamePreservesScorchingBloomDamageAndBlindRider(): void
    {
        $gift=(new PathGiftCatalogue())->all('circle-of-the-groveflame')[3];
        self::assertStringContainsString('4d8 fire damage',$gift['detail']);
        self::assertStringContainsString('become blinded',$gift['detail']);
    }

    public function testChurnPreservesTrueChurnformMaximization(): void
    {
        $gift=(new PathGiftCatalogue())->all('circle-of-the-churn')[3];
        self::assertStringContainsString('restore HP or deal cold damage are maximized',$gift['detail']);
    }

    public function testChurnCircleSpellCadenceIsCertifiedExactly(): void
    {
        self::assertSame(
            [
                ['level'=>3,'spells'=>['Ice Knife','Goodberry']],
                ['level'=>5,'spells'=>['Lesser Restoration',"Snilloc's Snowball Swarm"]],
                ['level'=>7,'spells'=>['Aura of Vitality','Sleet Storm']],
                ['level'=>9,'spells'=>['Freedom of Movement','Ice Storm']],
            ],
            (new DruidCircleSpellCatalogue())->forCircle('circle-of-the-churn')
        );
    }

    public function testChurnCircleSpellsUnlockByDruidLevel(): void
    {
        $spells=(new DruidCircleSpellCatalogue())->unlocked('circle-of-the-churn',5);
        self::assertSame(
            ['Ice Knife','Goodberry','Lesser Restoration',"Snilloc's Snowball Swarm"],
            $spells
        );
    }

    public function testOtherCirclesDoNotInventCircleSpells(): void
    {
        self::assertSame(
            [],
            (new DruidCircleSpellCatalogue())->forCircle('circle-of-the-compost')
        );
    }

    public function testCircleGuidesAndGiftPreviewsAreNowPopulated(): void
    {
        $paths=(new PathCandidateCatalogue())->forClass(CharacterClass::fromString('druid'));
        foreach($paths as $path) {
            self::assertNotSame('',(string)$path['identity']);
            self::assertNotSame('',(string)$path['playstyle']);
            self::assertNotSame('',(string)$path['best_for']);
            self::assertCount(4,$path['gift_preview']);
        }
    }

    public function testResourceMechanicsRemainCatalogueOnlyInThisSlice(): void
    {
        $definition=file_get_contents(
            dirname(__DIR__,6).'/app/Modules/Characters/Progression/Paths/Gifts/Definitions/DruidCircleGiftProgression.php'
        );
        self::assertIsString($definition);
        self::assertStringContainsString('Resource expenditure, Wild Shape interactions',$definition);
        self::assertStringNotContainsString('ActiveClassResourceState',$definition);
    }
}
