<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;
use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;

defined('ABSPATH') || exit;

final class RedeemFellowshipSealRequest extends FormRequest
{
    public function authorize(): bool
    {
        $userId = get_current_user_id();

        return $userId > 0
            && is_user_logged_in()
            && user_can($userId, 'gmrc_access_companion')
            && GuildProfile::accountType($userId) === AccountType::PLAYER;
    }

    public function rules(): array
    {
        return [
            'fellowship_seal' => [
                'required',
                'string',
                'min:8',
                'max:12',
            ],
            'character_id' => [
                'required',
                'string',
                'min:26',
                'max:26',
            ],
        ];
    }

    public function code(): string
    {
        return $this->validated()->string('fellowship_seal');
    }

    public function characterId(): string
    {
        return $this->validated()->string('character_id');
    }
}
