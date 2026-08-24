<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;
use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;

final class AssignCampaignCharacterRequest extends FormRequest
{
    public function authorize(): bool
    {
        $userId = get_current_user_id();

        return $userId > 0
            && user_can($userId, 'gmrc_access_companion')
            && GuildProfile::accountType($userId) === AccountType::PLAYER;
    }

    public function rules(): array
    {
        return [
            'character_id' => ['required', 'string', 'min:10', 'max:40'],
        ];
    }

    public function characterId(): string
    {
        return trim($this->validated()->string('character_id'));
    }
}
