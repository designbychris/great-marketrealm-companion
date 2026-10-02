<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;
use GreatMarketrealmCompanion\Core\Http\FormRequest;use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Persona;
defined('ABSPATH') || exit;
final class SavePersonaRequest extends FormRequest
{
 public function authorize():bool{return current_user_can('gmrc_manage_campaigns');}
 public function rules():array{return ['name'=>['required','string','min:2','max:140'],'record_kind'=>['required','string','in:'.implode(',',Persona::KINDS)],'role'=>['string','max:180'],'summary'=>['string','max:600'],'keeper_notes'=>['string','max:20000'],'visibility'=>['required','string','in:'.implode(',',Persona::VISIBILITIES)],'faction_id'=>['string','max:26']];}
 public function name():string{return trim($this->validated()->string('name'));}public function kind():string{return $this->validated()->string('record_kind','person');}public function role():string{return trim($this->validated()->string('role'));}public function summary():string{return trim($this->validated()->string('summary'));}public function notes():string{return trim($this->validated()->string('keeper_notes'));}public function visibility():string{return $this->validated()->string('visibility','keeper');}public function factionId():string{return trim($this->validated()->string('faction_id'));}
}
