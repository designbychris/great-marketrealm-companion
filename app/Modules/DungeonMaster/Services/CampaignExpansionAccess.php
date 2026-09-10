<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Services;

use GreatMarketrealmCompanion\Integration\Expansions\ExpansionCharacterCatalogue;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\CampaignExpansionRepository;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories\PlayerCampaignRepository;
use GreatMarketrealmCompanion\Modules\GuildGate\Services\GuildRoleRegistrar;

defined('ABSPATH') || exit;

/**
 * Resolve which active Almanacs may cross from a DM Campaign to its players.
 *
 * V.11A.2 deliberately keeps three decisions separate:
 * - GMREXP says whether an Almanac is site-active/available to consumers.
 * - the DM chooses whether that Almanac is shared with a Campaign.
 * - the Companion derives a player's consumable set from Campaign membership.
 *
 * A later entitlement provider can replace the current free/site-active
 * availability rule without changing Campaign sharing or Character content.
 */
final class CampaignExpansionAccess
{
    public function __construct(
        private CampaignExpansionRepository $campaignExpansions,
        private PlayerCampaignRepository $playerCampaigns,
        private ExpansionCharacterCatalogue $expansions
    ) {
    }

    /** @return string[] */
    public function availableToDungeonMaster(Campaign $campaign): array
    {
        if ($campaign->isArchived()) {
            return [];
        }

        return $this->expansions->activeExpansionKeys();
    }

    /** @return string[] */
    public function configuredForCampaign(Campaign $campaign): array
    {
        return $this->campaignExpansions->configured($campaign);
    }

    /**
     * Campaign-active means configured by the DM AND still site-active in
     * GMREXP. A temporarily unavailable Almanac remains configured so it can
     * resume automatically if the Keeper reactivates it later.
     *
     * @return string[]
     */
    public function activeForCampaign(Campaign $campaign): array
    {
        if ($campaign->isArchived()) {
            return [];
        }

        return array_values(array_intersect(
            $this->campaignExpansions->configured($campaign),
            $this->expansions->activeExpansionKeys()
        ));
    }

    /**
     * Save the DM's visible Campaign selection while preserving configured
     * Almanacs that are temporarily unavailable at site level.
     *
     * @param string[] $selected
     */
    public function saveCampaignSelection(
        Campaign $campaign,
        array $selected
    ): void {
        $available = $this->availableToDungeonMaster($campaign);
        $configured = $this->configuredForCampaign($campaign);
        $unavailableConfigured = array_diff($configured, $available);
        $selectedAvailable = array_intersect(
            $this->normaliseKeys($selected),
            $available
        );

        $this->campaignExpansions->save(
            $campaign,
            array_values(array_unique(array_merge(
                $unavailableConfigured,
                $selectedAvailable
            )))
        );
    }

    /**
     * A DM sees every currently active Almanac available to their account.
     * A Player sees the union shared by each active Campaign whose roster
     * contains them. This is the inheritance boundary used by Character
     * creation and its server-side validation.
     *
     * @return string[]
     */
    public function expansionKeysForUser(int $userId): array
    {
        if ($userId < 1) {
            return [];
        }

        if ($this->isDungeonMaster($userId)) {
            return $this->expansions->activeExpansionKeys();
        }

        $keys = [];
        foreach ($this->playerCampaigns->allForPlayer($userId) as $campaign) {
            if ($campaign->isArchived()) {
                continue;
            }

            foreach ($this->activeForCampaign($campaign) as $key) {
                $keys[$key] = true;
            }
        }

        $keys = array_keys($keys);
        sort($keys);

        return $keys;
    }

    /** @return array<int,array{key:string,label:string,enabled:bool,active:bool}> */
    public function shelf(Campaign $campaign): array
    {
        $available = $this->availableToDungeonMaster($campaign);
        $configured = $this->configuredForCampaign($campaign);
        $all = array_values(array_unique(array_merge($available, $configured)));
        sort($all);

        return array_map(
            fn (string $key): array => [
                'key' => $key,
                'label' => $this->label($key),
                'enabled' => in_array($key, $configured, true),
                'active' => in_array($key, $available, true),
            ],
            $all
        );
    }

    private function isDungeonMaster(int $userId): bool
    {
        if (function_exists('user_can')) {
            return user_can($userId, GuildRoleRegistrar::MANAGE_CAMPAIGNS)
                || user_can($userId, 'manage_options');
        }

        if (
            function_exists('get_current_user_id')
            && function_exists('current_user_can')
            && get_current_user_id() === $userId
        ) {
            return current_user_can(GuildRoleRegistrar::MANAGE_CAMPAIGNS)
                || current_user_can('manage_options');
        }

        return false;
    }

    /** @param array<int,mixed> $values @return string[] */
    private function normaliseKeys(array $values): array
    {
        $keys = [];
        foreach ($values as $value) {
            if (! is_scalar($value)) {
                continue;
            }

            $key = strtolower(trim((string) $value));
            $key = preg_replace('/[^a-z0-9_-]+/', '-', $key);
            $key = is_string($key) ? trim($key, '-') : '';

            if ($key !== '') {
                $keys[$key] = true;
            }
        }

        $keys = array_keys($keys);
        sort($keys);

        return $keys;
    }

    private function label(string $key): string
    {
        return ucwords(str_replace(['-', '_'], ' ', $key));
    }
}
