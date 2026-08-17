<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class UpdatePartyMemberOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return is_user_logged_in();
    }

    public function rules(): array
    {
        return [
            'office' => [
                'required',
                'string',
                'in:none,quartermaster,chronicler,pathfinder,standard-bearer',
            ],
        ];
    }

    public function office(): string
    {
        return $this->validated()->string('office');
    }
}
