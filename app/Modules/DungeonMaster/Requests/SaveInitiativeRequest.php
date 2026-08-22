<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class SaveInitiativeRequest extends FormRequest
{
    public function authorize(): bool { return current_user_can('gmrc_manage_campaigns'); }
    public function rules(): array
    {
        return [
            'initiative_action' => ['required', 'string', 'in:save,sort,advance,reset,complete'],
            'round' => ['required', 'integer', 'min:1', 'max:9999'],
            'turn_index' => ['required', 'integer', 'min:0', 'max:999'],
            'combatants' => ['array'],
        ];
    }
    public function action(): string { return $this->validated()->string('initiative_action', 'save'); }
    public function round(): int { return $this->validated()->integer('round', 1); }
    public function turnIndex(): int { return $this->validated()->integer('turn_index', 0); }
    /** @return array<mixed> */ public function combatants(): array { return $this->validated()->array('combatants'); }
}
