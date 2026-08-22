<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class SaveSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return current_user_can('gmrc_manage_campaigns');
    }

    public function rules(): array
    {
        return [
            'session_number' => ['required', 'integer', 'min:1', 'max:9999'],
            'title' => ['required', 'string', 'max:120'],
            'scheduled_date' => ['string', 'max:10'],
            'status' => ['required', 'string', 'in:planned,played,cancelled'],
            'prep_notes' => ['string', 'max:5000'],
            'recap' => ['string', 'max:10000'],
            'attendance_player_ids' => ['array'],
            'attendance_character_ids' => ['array'],
        ];
    }

    public function number(): int { return $this->validated()->integer('session_number', 1); }
    public function title(): string { return trim($this->validated()->string('title')); }
    public function status(): string { return $this->validated()->string('status', 'planned'); }
    public function prepNotes(): string { return trim($this->validated()->string('prep_notes')); }
    public function recap(): string { return trim($this->validated()->string('recap')); }
    /** @return array<mixed> */ public function playerIds(): array { return $this->validated()->array('attendance_player_ids'); }
    /** @return array<mixed> */ public function characterIds(): array { return $this->validated()->array('attendance_character_ids'); }

    public function scheduledDate(): string
    {
        $date = trim($this->validated()->string('scheduled_date'));
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1 ? $date : '';
    }
}
