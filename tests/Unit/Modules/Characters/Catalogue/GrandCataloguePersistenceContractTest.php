<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Tests\Unit\Modules\Characters\Catalogue;
use PHPUnit\Framework\TestCase;
final class GrandCataloguePersistenceContractTest extends TestCase
{
    public function testCatalogueSeedsWordPressDatabaseAndProfilesUsePostMeta(): void
    {
        $root=dirname(__DIR__,5);
        $catalogue=file_get_contents($root.'/app/Modules/Characters/Catalogue/Repositories/CharacterCatalogueRepository.php');
        $profiles=file_get_contents($root.'/app/Modules/Characters/Catalogue/Repositories/CharacterBuildProfileRepository.php');
        self::assertIsString($catalogue);self::assertIsString($profiles);
        self::assertStringContainsString("gmrc_character_catalogue",$catalogue);
        self::assertStringContainsString('update_option',$catalogue);
        self::assertStringContainsString('_gmrc_heritage',$profiles);
        self::assertStringContainsString('_gmrc_subclass',$profiles);
    }
}
