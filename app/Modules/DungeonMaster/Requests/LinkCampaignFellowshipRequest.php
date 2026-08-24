<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

final class LinkCampaignFellowshipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return current_user_can('gmrc_manage_campaigns');
    }

    public function rules(): array
    {
        return [
            'party_id' => ['required', 'string', 'min:10', 'max:40'],
        ];
    }

    public function partyId(): string
    {
        return trim($this->validated()->string('party_id'));
    }
}
