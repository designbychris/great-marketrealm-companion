<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;
use GreatMarketrealmCompanion\Modules\DungeonMaster\Models\MarketPass;
use GreatMarketrealmCompanion\Modules\GuildGate\AccountType;
use GreatMarketrealmCompanion\Modules\GuildGate\GuildProfile;

defined('ABSPATH') || exit;

final class RedeemMarketPassRequest extends FormRequest
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
            'market_pass' => ['required', 'string', 'min:8', 'max:12'],
        ];
    }

    public function code(): string
    {
        return MarketPass::normalise($this->validated()->string('market_pass'));
    }
}
