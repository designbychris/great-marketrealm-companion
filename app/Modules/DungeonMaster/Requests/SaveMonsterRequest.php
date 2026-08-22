<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class SaveMonsterRequest extends FormRequest
{
    public function authorize(): bool { return current_user_can('gmrc_manage_campaigns'); }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'creature_type' => ['string', 'max:80'],
            'size' => ['string', 'max:40'],
            'armor_class' => ['required', 'integer', 'min:0', 'max:99'],
            'max_hp' => ['required', 'integer', 'min:1', 'max:99999'],
            'speed' => ['string', 'max:120'],
            'strength' => ['required', 'integer', 'min:1', 'max:30'],
            'dexterity' => ['required', 'integer', 'min:1', 'max:30'],
            'constitution' => ['required', 'integer', 'min:1', 'max:30'],
            'intelligence' => ['required', 'integer', 'min:1', 'max:30'],
            'wisdom' => ['required', 'integer', 'min:1', 'max:30'],
            'charisma' => ['required', 'integer', 'min:1', 'max:30'],
            'challenge' => ['string', 'max:30'],
            'traits' => ['string', 'max:10000'],
            'actions' => ['string', 'max:10000'],
            'notes' => ['string', 'max:10000'],
        ];
    }

    public function name(): string { return trim($this->validated()->string('name')); }
    public function creatureType(): string { return trim($this->validated()->string('creature_type')); }
    public function size(): string { return trim($this->validated()->string('size')); }
    public function armorClass(): int { return $this->validated()->integer('armor_class', 10); }
    public function maxHp(): int { return $this->validated()->integer('max_hp', 1); }
    public function speed(): string { return trim($this->validated()->string('speed')); }
    public function strength(): int { return $this->validated()->integer('strength', 10); }
    public function dexterity(): int { return $this->validated()->integer('dexterity', 10); }
    public function constitution(): int { return $this->validated()->integer('constitution', 10); }
    public function intelligence(): int { return $this->validated()->integer('intelligence', 10); }
    public function wisdom(): int { return $this->validated()->integer('wisdom', 10); }
    public function charisma(): int { return $this->validated()->integer('charisma', 10); }
    public function challenge(): string { return trim($this->validated()->string('challenge')); }
    public function traits(): string { return trim($this->validated()->string('traits')); }
    public function actions(): string { return trim($this->validated()->string('actions')); }
    public function notes(): string { return trim($this->validated()->string('notes')); }
}
