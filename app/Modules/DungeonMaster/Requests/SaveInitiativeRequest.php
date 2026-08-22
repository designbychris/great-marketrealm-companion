<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class SaveInitiativeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return current_user_can('gmrc_manage_campaigns');
    }

    public function rules(): array
    {
        return [
            'initiative_action' => [
                'required',
                'string',
                'in:save,sort,advance,rewind,reset,complete,add,remove',
            ],
            'round' => ['required', 'integer', 'min:1', 'max:9999'],
            'turn_index' => ['required', 'integer', 'min:0', 'max:999'],
            'combatants' => ['array'],
            'remove_id' => ['string', 'max:120'],
            'new_name' => ['string', 'max:120'],
            'new_type' => ['string', 'in:adversary,ally'],
            'new_max_hp' => ['integer', 'min:0', 'max:99999'],
            'new_modifier' => ['integer', 'min:-20', 'max:20'],
        ];
    }

    public function action(): string
    {
        return $this->validated()->string('initiative_action', 'save');
    }

    public function round(): int { return $this->validated()->integer('round', 1); }
    public function turnIndex(): int { return $this->validated()->integer('turn_index', 0); }
    /** @return array<mixed> */
    public function combatants(): array { return $this->validated()->array('combatants'); }
    public function removeId(): string { return $this->validated()->string('remove_id', ''); }
    public function newName(): string { return $this->validated()->string('new_name', ''); }
    public function newType(): string { return $this->validated()->string('new_type', 'adversary'); }
    public function newMaxHp(): int { return $this->validated()->integer('new_max_hp', 0); }
    public function newModifier(): int { return $this->validated()->integer('new_modifier', 0); }
}
