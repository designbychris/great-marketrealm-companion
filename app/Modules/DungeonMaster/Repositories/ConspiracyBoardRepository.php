<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Repositories;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Campaign;use RuntimeException;
defined('ABSPATH') || exit;
final class ConspiracyBoardRepository
{
 private const META_POSITIONS='_gmrc_conspiracy_board_positions';
 public function __construct(private CampaignRepository $campaigns){}
 /** @return array<string,array{x:float,y:float}> */
 public function positions(Campaign $campaign):array{$postId=$this->postId($campaign);$raw=get_post_meta($postId,self::META_POSITIONS,true);$decoded=is_string($raw)?json_decode($raw,true):$raw;if(!is_array($decoded)){return [];} $positions=[];foreach($decoded as $key=>$value){if(!is_string($key)||!is_array($value)){continue;}$x=max(0,min(100,(float)($value['x']??0)));$y=max(0,min(100,(float)($value['y']??0)));$positions[$key]=['x'=>$x,'y'=>$y];}return $positions;}
 /** @param array<string,array{x:float,y:float}> $positions */
 public function savePositions(Campaign $campaign,array $positions):void{$clean=[];foreach($positions as $key=>$value){$key=sanitize_key((string)$key);if($key===''||!is_array($value)){continue;}$clean[$key]=['x'=>max(0,min(100,(float)($value['x']??0))),'y'=>max(0,min(100,(float)($value['y']??0)))];}update_post_meta($this->postId($campaign),self::META_POSITIONS,wp_json_encode($clean));}
 private function postId(Campaign $campaign):int{$postId=$this->campaigns->postIdForOwner($campaign->id(),$campaign->ownerId());if($postId===null){throw new RuntimeException('The Campaign Register record could not be found for this Conspiracy Board.');}return $postId;}
}
