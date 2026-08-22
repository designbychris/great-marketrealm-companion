<?php

declare(strict_types=1);
namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;
use GreatMarketrealmCompanion\Core\Http\FormRequest;
defined('ABSPATH') || exit;
class StoreCampaignRequest extends FormRequest
{
 public function authorize(): bool { return current_user_can('gmrc_manage_campaigns'); }
 public function rules(): array { return ['name'=>['required','string','min:2','max:100'],'description'=>['nullable','string','max:1200']]; }
 public function name(): string { return $this->validated()->string('name'); }
 public function description(): string { return $this->validated()->string('description'); }
}
