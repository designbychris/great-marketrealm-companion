<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Tests\Unit\Modules\GuildGate;

use PHPUnit\Framework\TestCase;

final class GuildProfileRegressionTest extends TestCase
{
    public function testProfileRoutesUseGuildGateController(): void
    {
        $routes = $this->source('app/Modules/GuildGate/Routes.php');
        self::assertStringContainsString("'/guild-profile'", $routes);
        self::assertStringContainsString("'/guild-profile/portrait'", $routes);
        self::assertStringContainsString("'updateProfile'", $routes);
        self::assertStringContainsString("'uploadPortrait'", $routes);
        self::assertStringContainsString("'removePortrait'", $routes);
    }

    public function testProfileUpdateCannotChangeGuildRole(): void
    {
        $view = $this->source('app/Modules/GuildGate/Views/profile.php');
        $service = $this->source('app/Modules/GuildGate/Services/UpdateGuildProfile.php');
        self::assertStringContainsString('cannot be changed from this profile form', $view);
        self::assertStringNotContainsString('account_type', $service);
        self::assertStringNotContainsString('set_role(', $service);
    }

    public function testProfileUpdateUsesWordPressUserAndMetaStorage(): void
    {
        $service = $this->source('app/Modules/GuildGate/Services/UpdateGuildProfile.php');
        self::assertStringContainsString('wp_update_user([', $service);
        self::assertStringContainsString("'display_name'", $service);
        self::assertStringContainsString("'user_email'", $service);
        self::assertStringContainsString("'gmrc_profile_bio'", $service);
        self::assertStringContainsString('email_exists($email)', $service);
    }

    public function testPortraitUploadHasTypeAndSizeGuards(): void
    {
        $service = $this->source('app/Modules/GuildGate/Services/GuildPortraitManager.php');
        self::assertStringContainsString('5 * MB_IN_BYTES', $service);
        self::assertStringContainsString('wp_check_filetype_and_ext(', $service);
        self::assertStringContainsString("'image/jpeg'", $service);
        self::assertStringContainsString("'image/png'", $service);
        self::assertStringContainsString("'image/webp'", $service);
        self::assertStringContainsString("media_handle_upload('gmrc_profile_portrait'", $service);
        self::assertStringContainsString('GuildProfile::PORTRAIT_ATTACHMENT_META', $service);
    }

    public function testRemovingPortraitRestoresFallbackWithoutDeletingMedia(): void
    {
        $service = $this->source('app/Modules/GuildGate/Services/GuildPortraitManager.php');
        self::assertStringContainsString('delete_user_meta(', $service);
        self::assertStringNotContainsString('wp_delete_attachment(', $service);
    }

    public function testProfileFormsHaveDedicatedNoncesAndUploadSemantics(): void
    {
        $view = $this->source('app/Modules/GuildGate/Views/profile.php');
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        self::assertStringContainsString("wp_nonce_field('gmrc_guild_profile_update'", $view);
        self::assertStringContainsString("wp_nonce_field('gmrc_guild_profile_portrait'", $view);
        self::assertStringContainsString('enctype="multipart/form-data"', $view);
        self::assertStringContainsString('accept="image/jpeg,image/png,image/webp"', $view);
        self::assertStringContainsString("'gmrc_guild_profile_update'", $frontend);
        self::assertStringContainsString("'gmrc_guild_profile_portrait'", $frontend);
    }

    public function testProfileStylesAreActuallyEnqueuedForSignedInApp(): void
    {
        $frontend = $this->source('app/Providers/FrontendServiceProvider.php');
        self::assertStringContainsString('$this->enqueueGuildProfile();', $frontend);
        self::assertStringContainsString("'gmrc-guild-profile'", $frontend);
        self::assertStringContainsString("'assets/css/modules/guild-gate/guild-profile.css'", $frontend);
    }

    public function testProfileCssCoversResponsiveAndAccessibilityStates(): void
    {
        $css = $this->source('assets/css/modules/guild-gate/guild-profile.css');
        self::assertStringContainsString('@media(max-width:760px)', $css);
        self::assertStringContainsString('@media(prefers-reduced-transparency:reduce)', $css);
        self::assertStringContainsString('@media(forced-colors:active)', $css);
        self::assertStringContainsString(':focus-visible', $css);
    }

    private function source(string $path): string
    {
        $source = file_get_contents(dirname(__DIR__, 4) . '/' . $path);
        self::assertIsString($source);
        return $source;
    }
}
