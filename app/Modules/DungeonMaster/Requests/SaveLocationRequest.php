<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;
use GreatMarketrealmCompanion\Core\Http\FormRequest;use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\Location;
defined('ABSPATH') || exit;
final class SaveLocationRequest extends FormRequest
{
 public function authorize():bool{return current_user_can('gmrc_manage_campaigns');}
 public function rules():array{return ['name'=>['required','string','min:2','max:140'],'location_type'=>['required','string','in:'.implode(',',Location::TYPES)],'summary'=>['string','max:500'],'keeper_notes'=>['string','max:20000'],'parent_id'=>['string','max:26'],'visibility'=>['required','string','in:'.implode(',',Location::VISIBILITIES)]];}
 public function name():string{return trim($this->validated()->string('name'));}public function type():string{return $this->validated()->string('location_type','other');}public function summary():string{return trim($this->validated()->string('summary'));}public function notes():string{return trim($this->validated()->string('keeper_notes'));}public function parentId():string{return trim($this->validated()->string('parent_id'));}public function visibility():string{return $this->validated()->string('visibility','keeper');}
}
