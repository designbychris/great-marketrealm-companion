<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Models;
use GreatMarketrealmCompanion\Core\Support\Ulid;use InvalidArgumentException;
defined('ABSPATH') || exit;
final class Persona
{
 public const KINDS=['person','faction']; public const VISIBILITIES=['keeper','players']; public const STATUSES=['active','archived'];
 private function __construct(private string $id,private string $campaignId,private int $ownerId,private string $name,private string $kind,private string $role,private string $summary,private string $notes,private string $visibility,private string $status,private string $factionId='')
 {if(!Ulid::isValid($id)||!Ulid::isValid($campaignId)||$ownerId<1){throw new InvalidArgumentException('Invalid Dramatis Personae identity.');}if(!in_array($kind,self::KINDS,true)||!in_array($visibility,self::VISIBILITIES,true)||!in_array($status,self::STATUSES,true)){throw new InvalidArgumentException('Invalid Dramatis Personae classification.');}if($factionId!==''&&!Ulid::isValid($factionId)){throw new InvalidArgumentException('Invalid faction identity.');}if($kind==='faction'&&$factionId!==''){throw new InvalidArgumentException('A faction cannot belong to another faction in this register.');}}
 public static function create(string $campaignId,int $ownerId,string $name,string $kind,string $role,string $summary,string $notes,string $visibility,string $factionId=''):self{return new self(Ulid::generate(),$campaignId,$ownerId,$name,$kind,$role,$summary,$notes,$visibility,'active',$factionId);}
 public static function restore(string $id,string $campaignId,int $ownerId,string $name,string $kind,string $role,string $summary,string $notes,string $visibility,string $status,string $factionId=''):self{return new self($id,$campaignId,$ownerId,$name,$kind,$role,$summary,$notes,$visibility,$status,$factionId);}
 public function update(string $name,string $kind,string $role,string $summary,string $notes,string $visibility,string $factionId=''):void{if(!in_array($kind,self::KINDS,true)||!in_array($visibility,self::VISIBILITIES,true)){throw new InvalidArgumentException('Invalid Dramatis Personae classification.');}if($kind==='faction'){$factionId='';}$this->name=$name;$this->kind=$kind;$this->role=$role;$this->summary=$summary;$this->notes=$notes;$this->visibility=$visibility;$this->factionId=$factionId;}
 public function archive():void{$this->status='archived';}
 public function id():string{return $this->id;}public function campaignId():string{return $this->campaignId;}public function ownerId():int{return $this->ownerId;}public function name():string{return $this->name;}public function kind():string{return $this->kind;}public function role():string{return $this->role;}public function summary():string{return $this->summary;}public function notes():string{return $this->notes;}public function visibility():string{return $this->visibility;}public function status():string{return $this->status;}public function factionId():string{return $this->factionId;}public function isArchived():bool{return $this->status==='archived';}public function isKeeperOnly():bool{return $this->visibility==='keeper';}public function isFaction():bool{return $this->kind==='faction';}public function kindLabel():string{return $this->isFaction()?'Faction':'Person';}
}
