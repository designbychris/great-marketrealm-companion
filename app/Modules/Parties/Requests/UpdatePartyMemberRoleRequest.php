<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class UpdatePartyMemberRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return is_user_logged_in();
    }

    public function rules(): array
    {
        return [
            'role' => [
                'required',
                'string',
                'in:leader,member',
            ],
        ];
    }

    public function role(): string
    {
        return $this->validated()->string('role');
    }
}
