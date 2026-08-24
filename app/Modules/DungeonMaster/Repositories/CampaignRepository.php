<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use RuntimeException;
use WP_Post;

defined('ABSPATH') || exit;

final class CampaignRepository
{
    public const POST_TYPE = 'gmrc_campaign';
    private const META_ID = '_gmrc_campaign_id';
    private const META_DESCRIPTION = '_gmrc_campaign_description';
    private const META_STATUS = '_gmrc_campaign_status';

    /** @return Campaign[] */
    public function all(): array
    {
        $posts = get_posts([
            'post_type' => self::POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        ]);

        return array_map(
            fn (WP_Post $post): Campaign => $this->map($post),
            $posts
        );
    }

    /** @return Campaign[] */
    public function allForOwner(int $ownerId): array
    {
        $posts = get_posts(['post_type'=>self::POST_TYPE,'post_status'=>'publish','posts_per_page'=>-1,'author'=>$ownerId,'orderby'=>'date','order'=>'DESC']);
        return array_map(fn(WP_Post $post): Campaign => $this->map($post), $posts);
    }

    public function findForOwner(string $id, int $ownerId): ?Campaign
    {
        $posts = get_posts(['post_type'=>self::POST_TYPE,'post_status'=>'publish','posts_per_page'=>2,'author'=>$ownerId,'meta_key'=>self::META_ID,'meta_value'=>$id]);
        if (count($posts) > 1) { throw new RuntimeException('The Campaign Register contains duplicate campaign records.'); }
        $post=$posts[0]??null;
        return $post instanceof WP_Post ? $this->map($post) : null;
    }

    public function postIdForOwner(string $id, int $ownerId): ?int
    {
        $post = $this->postForOwner($id, $ownerId);

        return $post instanceof WP_Post
            ? (int) $post->ID
            : null;
    }

    public function findByPostId(int $postId): ?Campaign
    {
        $post = get_post($postId);

        if (! $post instanceof WP_Post || $post->post_type !== self::POST_TYPE) {
            return null;
        }

        return $this->map($post);
    }

    public function save(Campaign $campaign): void
    {
        $existing=$this->postForOwner($campaign->id(),$campaign->ownerId());
        $payload=['post_type'=>self::POST_TYPE,'post_status'=>'publish','post_title'=>$campaign->name(),'post_author'=>$campaign->ownerId()];
        if ($existing instanceof WP_Post) { $payload['ID']=$existing->ID; $postId=wp_update_post($payload,true); }
        else { $postId=wp_insert_post($payload,true); }
        if (is_wp_error($postId) || (int)$postId < 1) { throw new RuntimeException('The campaign could not be written to the Register.'); }
        update_post_meta((int)$postId,self::META_ID,$campaign->id());
        update_post_meta((int)$postId,self::META_DESCRIPTION,$campaign->description());
        update_post_meta((int)$postId,self::META_STATUS,$campaign->status());
    }

    private function postForOwner(string $id,int $ownerId): ?WP_Post
    {
        $posts=get_posts(['post_type'=>self::POST_TYPE,'post_status'=>'publish','posts_per_page'=>2,'author'=>$ownerId,'meta_key'=>self::META_ID,'meta_value'=>$id]);
        if(count($posts)>1){throw new RuntimeException('The Campaign Register contains duplicate campaign records.');}
        $post=$posts[0]??null; return $post instanceof WP_Post ? $post : null;
    }

    private function map(WP_Post $post): Campaign
    {
        return Campaign::restore((string)get_post_meta($post->ID,self::META_ID,true),(string)$post->post_title,(int)$post->post_author,(string)get_post_meta($post->ID,self::META_DESCRIPTION,true),(string)get_post_meta($post->ID,self::META_STATUS,true));
    }
}
