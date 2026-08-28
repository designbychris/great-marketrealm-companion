<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\GuildGate;

use PHPUnit\Framework\TestCase;

final class TabletopAvatarBridgeRegressionTest extends TestCase
{
    public function testFrontendProviderHooksCompanionProfilePortraitIntoTabletopAvatarBoundary(): void
    {
        $provider = file_get_contents(dirname(__DIR__, 4) . '/app/Providers/FrontendServiceProvider.php');

        self::assertIsString($provider);
        self::assertStringContainsString("'gmrt_table_member_avatar_url'", $provider);
        self::assertStringContainsString("[\$this, 'tabletopMemberAvatarUrl']", $provider);
        self::assertStringContainsString('GuildProfile::portraitAttachmentId($userId)', $provider);
        self::assertStringContainsString('wp_get_attachment_image_url($portraitId, [64, 64])', $provider);
    }
}
