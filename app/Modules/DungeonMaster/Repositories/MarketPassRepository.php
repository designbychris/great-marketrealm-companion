<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories;

use GreatMarketrealmCompanion\Core\Invitations\InviteCodeGenerator;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\MarketPass;
use RuntimeException;
use WP_Post;

defined('ABSPATH') || exit;

final class MarketPassRepository
{
    private const META_PASS = '_gmrc_market_pass';
    private const META_LOOKUP = '_gmrc_market_pass_lookup';
    private const GENERATION_ATTEMPTS = 12;

    public function __construct(
        private CampaignRepository $campaigns,
        private InviteCodeGenerator $codes
    ) {
    }

    public function current(Campaign $campaign): ?MarketPass
    {
        $stored = get_post_meta($this->postId($campaign), self::META_PASS, true);

        if (! is_array($stored)) {
            return null;
        }

        $code = (string) ($stored['code'] ?? '');
        $issuedAt = (int) ($stored['issued_at'] ?? 0);
        $expiresAt = (int) ($stored['expires_at'] ?? 0);

        if (strlen(MarketPass::normalise($code)) !== 8
            || $issuedAt < 1
            || $expiresAt <= $issuedAt) {
            return null;
        }

        return MarketPass::restore(
            $code,
            $issuedAt,
            $expiresAt,
            (string) ($stored['status'] ?? MarketPass::STATUS_ACTIVE)
        );
    }

    public function issue(Campaign $campaign): MarketPass
    {
        for ($attempt = 0; $attempt < self::GENERATION_ATTEMPTS; $attempt++) {
            $pass = MarketPass::issue($this->codes->generate());

            if (! $this->lookupExists($pass->lookupCode())) {
                $this->write($campaign, $pass);
                return $pass;
            }
        }

        throw new RuntimeException('A unique Market Pass could not be issued. Please try again.');
    }

    public function revoke(Campaign $campaign): void
    {
        $pass = $this->current($campaign);

        if (! $pass instanceof MarketPass) {
            return;
        }

        $pass->revoke();
        $this->write($campaign, $pass);
    }

    public function campaignForCode(string $submittedCode): ?Campaign
    {
        $lookup = MarketPass::normalise($submittedCode);

        if (strlen($lookup) !== 8) {
            return null;
        }

        $posts = get_posts([
            'post_type' => CampaignRepository::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 2,
            'meta_key' => self::META_LOOKUP,
            'meta_value' => $lookup,
        ]);

        if (count($posts) !== 1 || ! $posts[0] instanceof WP_Post) {
            return null;
        }

        $campaign = $this->campaigns->findByPostId((int) $posts[0]->ID);

        if (! $campaign instanceof Campaign) {
            return null;
        }

        $pass = $this->current($campaign);

        return $pass instanceof MarketPass
            && hash_equals($pass->lookupCode(), $lookup)
            && $pass->isRedeemable()
                ? $campaign
                : null;
    }

    private function write(Campaign $campaign, MarketPass $pass): void
    {
        $postId = $this->postId($campaign);

        update_post_meta($postId, self::META_PASS, [
            'code' => $pass->code(),
            'issued_at' => $pass->issuedAt(),
            'expires_at' => $pass->expiresAt(),
            'status' => $pass->status(),
        ]);
        update_post_meta($postId, self::META_LOOKUP, $pass->lookupCode());
    }

    private function lookupExists(string $lookup): bool
    {
        $posts = get_posts([
            'post_type' => CampaignRepository::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'meta_key' => self::META_LOOKUP,
            'meta_value' => $lookup,
        ]);

        return $posts !== [];
    }

    private function postId(Campaign $campaign): int
    {
        $postId = $this->campaigns->postIdForOwner($campaign->id(), $campaign->ownerId());

        if ($postId === null) {
            throw new RuntimeException('The Campaign Register record could not be found.');
        }

        return $postId;
    }
}
