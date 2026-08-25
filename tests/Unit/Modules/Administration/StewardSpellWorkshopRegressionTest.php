<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Administration;
use PHPUnit\Framework\TestCase;

final class StewardSpellWorkshopRegressionTest extends TestCase
{
    private string $root,$workshop,$shared,$provider,$view,$bridge;
    protected function setUp():void{$this->root=dirname(__DIR__,4);$this->workshop=$this->source('app/Modules/Administration/Workshop/SpellWorkshop.php');$this->shared=$this->source('app/Modules/Library/Spells/Repositories/SharedSpellRegister.php');$this->provider=$this->source('app/Providers/AdministrationServiceProvider.php');$this->view=$this->source('app/Modules/Administration/Views/spell-workshop.php');$this->bridge=$this->source('app/Modules/Characters/Arcana/Services/StewardSpellAbilityBridge.php');}
    public function testWorkshopUsesSeparatePersistentRegistry():void{self::assertStringContainsString('gmrc_steward_spells',$this->workshop);self::assertStringNotContainsString('gmrc_canonical_spell_overrides',$this->workshop);}
    public function testWorkshopSupportsPublicationLifecycle():void{foreach(['draft','published','archived'] as $s)self::assertStringContainsString($s,$this->workshop);}
    public function testPublishedSpellsRequireMechanicalIdentity():void{foreach(['level','school','access','casting time','range','duration','rules text'] as $text)self::assertStringContainsString($text,$this->workshop);}
    public function testSharedRegisterMergesCanonAndPublishedStewardSpells():void{self::assertStringContainsString('CanonicalSpellRegister',$this->shared);self::assertStringContainsString('->published()',$this->shared);}
    public function testStewardKeysCannotOverwriteCanonicalSpellIdentity():void{self::assertStringContainsString("'steward-spell-'",$this->workshop);self::assertStringContainsString('$this->canonical->find($key)',$this->workshop);}
    public function testWorkshopIsAdministratorProtectedAndNonceVerified():void{self::assertStringContainsString('admin_post_gmrc_save_steward_spell',$this->provider);self::assertStringContainsString("check_admin_referer('gmrc_save_steward_spell_",$this->provider);}
    public function testWorkshopCoversSpellRulesAndClassAccess():void{foreach(['casting_time','components','higher_levels','access_labels[]','concentration','ritual'] as $field)self::assertStringContainsString($field,$this->view);}
    public function testWorkshopSupportsOptionalLiveRollMetadata():void{foreach(['roll_kind','formula','spell_attack','add_casting_modifier'] as $field)self::assertStringContainsString($field,$this->view);}
    public function testMechanicallyReadyStewardSpellsBridgeIntoCharacters():void{self::assertStringContainsString('mechanicsReady()',$this->bridge);self::assertStringContainsString('ArcaneAbilityDefinition',$this->bridge);self::assertStringContainsString('StewardSpellAbilityBridge',$this->source('app/Modules/Characters/Arcana/Models/ArcaneAbilityCatalogue.php'));}
    public function testGuildSpellbookUsesSharedRegisterWithoutDiscardingCanonicalOverlay():void{foreach(['app/Modules/Library/Spells/Services/SpellbookPresenter.php','app/Modules/Library/Catalogues/SpellReferenceCatalogue.php','app/Modules/Characters/Arcana/Services/CanonicalSpellReferenceResolver.php'] as $path){$src=$this->source($path);self::assertStringContainsString('SharedSpellRegister',$src);self::assertStringContainsString('CanonicalSpellRegister',$src);}}
    private function source(string $p):string{$s=file_get_contents($this->root.'/'.$p);self::assertIsString($s);return $s;}
}
