<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class UpdatePartyCharterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return is_user_logged_in();
    }

    public function rules(): array
    {
        return [
            'motto' => [
                'nullable',
                'string',
                'max:90',
            ],
            'description' => [
                'nullable',
                'string',
                'max:240',
            ],
            'statement' => [
                'nullable',
                'string',
                'max:1200',
            ],
        ];
    }

    public function motto(): string
    {
        return $this->validated()->string('motto');
    }

    public function description(): string
    {
        return $this->validated()->string('description');
    }

    public function statement(): string
    {
        return $this->validated()->string('statement');
    }
}
