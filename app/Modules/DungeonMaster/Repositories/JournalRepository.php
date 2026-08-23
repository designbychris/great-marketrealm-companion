<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories;

use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\JournalEntry;
use RuntimeException;
use WP_Post;

defined('ABSPATH') || exit;

final class JournalRepository
{
    public const POST_TYPE='gmrc_dm_journal';
    private const META_ID='_gmrc_journal_id'; private const META_CAMPAIGN='_gmrc_journal_campaign_id';
    private const META_CATEGORY='_gmrc_journal_category'; private const META_STATUS='_gmrc_journal_status';
    private const META_SESSION='_gmrc_journal_session_id'; private const META_PINNED='_gmrc_journal_pinned';
    public function __construct(private CampaignRepository $campaigns) {}
    /** @return JournalEntry[] */
    public function allForCampaign(Campaign $campaign): array
    {
        $posts=get_posts(['post_type'=>self::POST_TYPE,'post_status'=>'publish','posts_per_page'=>-1,'author'=>$campaign->ownerId(),'meta_key'=>self::META_CAMPAIGN,'meta_value'=>$campaign->id(),'orderby'=>'modified','order'=>'DESC']);
        $entries=array_map(fn(WP_Post $post): JournalEntry=>$this->map($post),$posts);
        usort($entries,static fn(JournalEntry $a,JournalEntry $b): int => ($b->pinned()<=>$a->pinned()));
        return $entries;
    }
    public function findForCampaign(string $id, Campaign $campaign): ?JournalEntry
    { $post=$this->findPost($id,$campaign); return $post instanceof WP_Post ? $this->map($post) : null; }
    public function save(JournalEntry $entry, Campaign $campaign): void
    {
        if($entry->campaignId()!==$campaign->id()||$entry->ownerId()!==$campaign->ownerId()){throw new RuntimeException('The Journal entry does not belong to this Campaign.');}
        $parent=$this->campaigns->postIdForOwner($campaign->id(),$campaign->ownerId()); if($parent===null){throw new RuntimeException('The Campaign Register record could not be found.');}
        $existing=$this->findPost($entry->id(),$campaign);$payload=['post_type'=>self::POST_TYPE,'post_status'=>'publish','post_title'=>$entry->title(),'post_content'=>$entry->content(),'post_author'=>$campaign->ownerId(),'post_parent'=>$parent];
        if($existing instanceof WP_Post){$payload['ID']=$existing->ID;$postId=wp_update_post($payload,true);}else{$postId=wp_insert_post($payload,true);} if(is_wp_error($postId)||(int)$postId<1){throw new RuntimeException('The note could not be written to the Campaign Journal.');}
        foreach([self::META_ID=>$entry->id(),self::META_CAMPAIGN=>$campaign->id(),self::META_CATEGORY=>$entry->category(),self::META_STATUS=>$entry->status(),self::META_SESSION=>$entry->sessionId(),self::META_PINNED=>$entry->pinned()?'1':'0'] as $key=>$value){update_post_meta((int)$postId,$key,$value);}
    }
    private function findPost(string $id, Campaign $campaign): ?WP_Post
    {
        $posts=get_posts(['post_type'=>self::POST_TYPE,'post_status'=>'publish','posts_per_page'=>2,'author'=>$campaign->ownerId(),'meta_query'=>[['key'=>self::META_ID,'value'=>$id],['key'=>self::META_CAMPAIGN,'value'=>$campaign->id()]]]);
        if(count($posts)>1){throw new RuntimeException('The Campaign Journal contains duplicate records.');}$post=$posts[0]??null;return $post instanceof WP_Post?$post:null;
    }
    private function map(WP_Post $post): JournalEntry
    { return JournalEntry::restore((string)get_post_meta($post->ID,self::META_ID,true),(string)get_post_meta($post->ID,self::META_CAMPAIGN,true),(int)$post->post_author,(string)$post->post_title,(string)get_post_meta($post->ID,self::META_CATEGORY,true),(string)$post->post_content,(string)get_post_meta($post->ID,self::META_STATUS,true),(string)get_post_meta($post->ID,self::META_SESSION,true),get_post_meta($post->ID,self::META_PINNED,true)==='1'); }
}