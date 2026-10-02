<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Models;
use GreatMarketrealmCompanion\Core\Support\Ulid;
use InvalidArgumentException;
defined('ABSPATH') || exit;
final class Location
{
 public const TYPES=['realm','region','settlement','district','landmark','building','room','dungeon','wilderness','other'];
 public const VISIBILITIES=['keeper','players']; public const STATUSES=['active','archived'];
 private function __construct(private string $id,private string $campaignId,private int $ownerId,private string $name,private string $type,private string $summary,private string $notes,private string $parentId,private string $visibility,private string $status,private string $tabletopSceneId='')
 {if(!Ulid::isValid($id)||!Ulid::isValid($campaignId)||$ownerId<1){throw new InvalidArgumentException('Invalid Gazetteer location identity.');}if(!in_array($type,self::TYPES,true)||!in_array($visibility,self::VISIBILITIES,true)||!in_array($status,self::STATUSES,true)){throw new InvalidArgumentException('Invalid Gazetteer location classification.');}if($parentId!==''&&!Ulid::isValid($parentId)){throw new InvalidArgumentException('Invalid parent location identity.');}}
 public static function create(string $campaignId,int $ownerId,string $name,string $type,string $summary,string $notes,string $parentId,string $visibility): self{return new self(Ulid::generate(),$campaignId,$ownerId,$name,$type,$summary,$notes,$parentId,$visibility,'active');}
 public static function restore(string $id,string $campaignId,int $ownerId,string $name,string $type,string $summary,string $notes,string $parentId,string $visibility,string $status,string $tabletopSceneId=''): self{return new self($id,$campaignId,$ownerId,$name,$type,$summary,$notes,$parentId,$visibility,$status,$tabletopSceneId);}
 public function update(string $name,string $type,string $summary,string $notes,string $parentId,string $visibility): void{if(!in_array($type,self::TYPES,true)||!in_array($visibility,self::VISIBILITIES,true)){throw new InvalidArgumentException('Invalid Gazetteer location classification.');}$this->name=$name;$this->type=$type;$this->summary=$summary;$this->notes=$notes;$this->parentId=$parentId;$this->visibility=$visibility;}
 public function archive(): void{$this->status='archived';}
 public function id():string{return $this->id;} public function campaignId():string{return $this->campaignId;} public function ownerId():int{return $this->ownerId;} public function name():string{return $this->name;} public function type():string{return $this->type;} public function summary():string{return $this->summary;} public function notes():string{return $this->notes;} public function parentId():string{return $this->parentId;} public function visibility():string{return $this->visibility;} public function status():string{return $this->status;} public function tabletopSceneId():string{return $this->tabletopSceneId;}
 public function typeLabel():string{return ucwords(str_replace('-',' ',$this->type));} public function isArchived():bool{return $this->status==='archived';} public function isKeeperOnly():bool{return $this->visibility==='keeper';}
}
