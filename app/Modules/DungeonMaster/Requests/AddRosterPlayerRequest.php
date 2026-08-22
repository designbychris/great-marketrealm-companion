<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\DungeonMaster\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class AddRosterPlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return current_user_can('gmrc_manage_campaigns');
    }

    public function rules(): array
    {
        return [
            'guild_identity' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],
        ];
    }

    public function identity(): string
    {
        return trim(
            $this->validated()->string('guild_identity')
        );
    }
}
