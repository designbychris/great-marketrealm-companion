<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;
use GreatMarketrealmCompanion\Core\Http\FormRequest;use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\EvidenceRecord;
defined('ABSPATH') || exit;
final class SaveEvidenceRequest extends FormRequest
{
 public function authorize():bool{return current_user_can('gmrc_manage_campaigns');}
 public function rules():array{return ['name'=>['required','string','min:2','max:180'],'record_type'=>['required','string','in:'.implode(',',EvidenceRecord::RECORD_TYPES)],'evidence_kind'=>['string','in:'.implode(',',EvidenceRecord::EVIDENCE_KINDS)],'summary'=>['string','max:900'],'keeper_notes'=>['string','max:20000'],'visibility'=>['required','string','in:'.implode(',',EvidenceRecord::VISIBILITIES)],'thread_ids'=>['array'],'location_ids'=>['array'],'persona_ids'=>['array']];}
 public function name():string{return trim($this->validated()->string('name'));}public function recordType():string{return $this->validated()->string('record_type','evidence');}public function evidenceKind():string{return $this->validated()->string('evidence_kind','clue');}public function summary():string{return trim($this->validated()->string('summary'));}public function notes():string{return trim($this->validated()->string('keeper_notes'));}public function visibility():string{return $this->validated()->string('visibility','keeper');}/** @return list<string> */public function threadIds():array{return $this->strings('thread_ids');}/** @return list<string> */public function locationIds():array{return $this->strings('location_ids');}/** @return list<string> */public function personaIds():array{return $this->strings('persona_ids');}
 /** @return list<string> */private function strings(string $key):array{$value=$this->validated()->array($key);if(!is_array($value)){return [];}return array_values(array_unique(array_filter(array_map(static fn($v):string=>trim((string)$v),$value),static fn(string $v):bool=>$v!=='')));}
}
