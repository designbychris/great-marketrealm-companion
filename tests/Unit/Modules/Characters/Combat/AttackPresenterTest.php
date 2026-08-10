<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Combat;

use GreatMarketrealmCompanion\Modules\Characters\Combat\Services\AttackPresenter;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\CharacterInventory;
use GreatMarketrealmCompanion\Modules\Characters\Inventory\Models\ItemCatalogue;
use GreatMarketrealmCompanion\Modules\Characters\Models\Character;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScore;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\AbilityScores;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterClass;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterId;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\CharacterName;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\HitPoints;
use GreatMarketrealmCompanion\Modules\Characters\Models\ValueObjects\Race;
use PHPUnit\Framework\TestCase;

final class AttackPresenterTest extends TestCase
{
    public function testEquippedWeaponBecomesAnAttack(): void
    {
        $catalogue = new ItemCatalogue();
        $inventory = CharacterInventory::empty()->add('market-cleaver')->equip('market-cleaver', $catalogue);
        $attacks = (new AttackPresenter($catalogue))->present($this->character(), $inventory);
        self::assertCount(1, $attacks);
        self::assertSame('Market Cleaver', $attacks[0]['label']);
        self::assertSame(5, $attacks[0]['attack_bonus']);
        self::assertSame('1d6', $attacks[0]['damage_die']);
        self::assertSame(3, $attacks[0]['damage_modifier']);
    }

    public function testFinesseUsesDexterityWhenItIsBetter(): void
    {
        $catalogue = new ItemCatalogue();
        $inventory = CharacterInventory::empty()->add('paring-knife')->equip('paring-knife', $catalogue);
        $attacks = (new AttackPresenter($catalogue))->present($this->character(12, 18), $inventory);
        self::assertSame('Dexterity', $attacks[0]['ability']);
        self::assertSame(6, $attacks[0]['attack_bonus']);
        self::assertSame(4, $attacks[0]['damage_modifier']);
    }

    public function testUnequippedWeaponDoesNotBecomeAnAttack(): void
    {
        $catalogue = new ItemCatalogue();
        $inventory = CharacterInventory::empty()->add('market-cleaver');
        self::assertSame([], (new AttackPresenter($catalogue))->present($this->character(), $inventory));
    }

    private function character(int $strength = 16, int $dexterity = 14): Character
    {
        return Character::create(CharacterId::generate(), CharacterName::fromString('Wiz'), Race::fromString('fructan'), CharacterClass::fromString('fighter'), HitPoints::full(10), AbilityScores::fromScores(AbilityScore::fromInt($strength), AbilityScore::fromInt($dexterity), AbilityScore::fromInt(12), AbilityScore::fromInt(10), AbilityScore::fromInt(10), AbilityScore::fromInt(10)));
    }
}
