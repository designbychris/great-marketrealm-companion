<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class AddPartyMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return is_user_logged_in();
    }

    public function rules(): array
    {
        return [
            'character_id' => [
                'required',
                'string',
                'min:26',
                'max:26',
            ],
            'role' => [
                'required',
                'string',
                'in:leader,member',
            ],
        ];
    }

    public function characterId(): string
    {
        return $this->validated()->string(
            'character_id'
        );
    }

    public function role(): string
    {
        return $this->validated()->string('role');
    }
}
