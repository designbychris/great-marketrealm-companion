<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Providers;

use GreatMarketrealmCompanion\Services\Guild\GuildSealRegistry;

/**
 * @var iterable          $characters
 * @var string            $companionUrl
 * @var GuildSealRegistry $sealRegistry
 */

defined('ABSPATH') || exit;

/**
 * Guild Service Provider.
 *
 * Registers services belonging to the Guild domain.
 *
 * @since 0.3.0
 */
final class GuildServiceProvider extends ServiceProvider
{
    /**
     * Register Guild services.
     */
    public function register(): void
    {
        $this->app->container()->singleton(
            GuildSealRegistry::class,
            static function (): GuildSealRegistry {
                return new GuildSealRegistry();
            }
        );
    }

    /**
     * Boot Guild services.
     */
    public function boot(): void
    {
    }
}
