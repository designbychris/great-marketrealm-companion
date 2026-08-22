<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class SaveEncounterRequest extends FormRequest
{
    public function authorize(): bool { return current_user_can('gmrc_manage_campaigns'); }
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'session_id' => ['string', 'max:26'],
            'status' => ['required', 'string', 'in:prepared,running,completed'],
            'threat' => ['required', 'string', 'in:low,moderate,high,deadly'],
            'location' => ['string', 'max:160'],
            'adversaries' => ['string', 'max:5000'],
            'notes' => ['string', 'max:10000'],
            'character_ids' => ['array'],
        ];
    }
    public function title(): string { return trim($this->validated()->string('title')); }
    public function sessionId(): string { return trim($this->validated()->string('session_id')); }
    public function status(): string { return $this->validated()->string('status', 'prepared'); }
    public function threat(): string { return $this->validated()->string('threat', 'moderate'); }
    public function location(): string { return trim($this->validated()->string('location')); }
    public function adversaries(): string { return trim($this->validated()->string('adversaries')); }
    public function notes(): string { return trim($this->validated()->string('notes')); }
    /** @return array<mixed> */ public function characterIds(): array { return $this->validated()->array('character_ids'); }
}
