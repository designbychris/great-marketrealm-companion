<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\GuildGate;

use GreatMarketrealmCompanion\Core\Container;
use GreatMarketrealmCompanion\Modules\Administration\Security\GateSecuritySettings;
use GreatMarketrealmCompanion\Modules\GuildGate\Controllers\GuildGateController;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\AuthenticateGuildMember;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildAdminBarVisibility;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildPortraitManager;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildMembershipSummary;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildGateAudit;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildRoleRegistrar;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\RegisterGuildMember;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\UpdateGuildProfile;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\TurnstileVerifier;
use GreatMarketrealmCompanion\Providers\ServiceProvider;
use GreatMarketrealmCompanion\Modules\Characters\Repositories\CharacterRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\PlayerCampaignRepository;
use GreatMarketrealmCompanion\Modules\Parties\Services\SharedFellowshipAccess;

defined('ABSPATH') || exit;

final class GuildGateServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(GuildRoleRegistrar::class);
        $this->app->singleton(GuildAdminBarVisibility::class);
        $this->app->singleton(AuthenticateGuildMember::class);
        $this->app->singleton(RegisterGuildMember::class);
        $this->app->singleton(UpdateGuildProfile::class);
        $this->app->singleton(GuildPortraitManager::class);
        $this->app->singleton(
            GuildMembershipSummary::class,
            static fn (Container $container): GuildMembershipSummary =>
                new GuildMembershipSummary(
                    $container->make(CharacterRepository::class),
                    $container->make(CampaignRepository::class),
                    $container->make(PlayerCampaignRepository::class),
                    $container->make(SharedFellowshipAccess::class)
                )
        );
        $this->app->singleton(GuildGateAudit::class);
        $this->app->singleton(TurnstileVerifier::class);
        $this->app->bind(
            GuildGateController::class,
            static fn (Container $container): GuildGateController =>
                new GuildGateController(
                    $container->make(\GreatMarketrealmCompanion\Core\View\ViewFactory::class),
                    $container->make(\GreatMarketrealmCompanion\Core\Http\Request::class),
                    $container->make(\GreatMarketrealmCompanion\Core\Routing\Router::class),
                    $container->make(\GreatMarketrealmCompanion\Core\Http\ResponseFactory::class),
                    $container->make(\GreatMarketrealmCompanion\Core\Session\FlashStore::class),
                    $container->make(AuthenticateGuildMember::class),
                    $container->make(RegisterGuildMember::class),
                    $container->make(UpdateGuildProfile::class),
                    $container->make(GuildPortraitManager::class),
                    $container->make(GuildMembershipSummary::class),
                    $container->make(GateSecuritySettings::class),
                    $container->make(TurnstileVerifier::class),
                    $container->make(GuildGateAudit::class)
                )
        );
    }

    public function boot(): void
    {
        $this->app->make(GuildRoleRegistrar::class)->register();

        add_action('wp_enqueue_scripts', [$this, 'enqueueTurnstile']);

        add_filter(
            'show_admin_bar',
            [$this->app->make(GuildAdminBarVisibility::class), 'filter']
        );
    }

    public function enqueueTurnstile(): void
    {
        $settings = $this->app->make(GateSecuritySettings::class);
        if (is_user_logged_in() || ! $settings->configured()) {
            return;
        }
        $configuration = $settings->all();
        if (! $configuration['protect_registration'] && ! $configuration['protect_login']) {
            return;
        }
        wp_enqueue_script(
            'gmrc-cloudflare-turnstile',
            'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit',
            [],
            null,
            true
        );
    }
}
