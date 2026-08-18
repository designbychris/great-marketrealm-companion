<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class AddPartyChronicleNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return is_user_logged_in();
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:120',
            ],
            'content' => [
                'required',
                'string',
                'max:3000',
            ],
        ];
    }

    public function title(): string
    {
        return $this->validated()->string('title');
    }

    public function content(): string
    {
        return $this->validated()->string('content');
    }
}
