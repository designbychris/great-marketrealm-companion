<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class DepositPartyTreasuryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return is_user_logged_in();
    }

    public function rules(): array
    {
        return [
            'gold' => ['required', 'integer', 'min:0', 'max:999999'],
            'silver' => ['required', 'integer', 'min:0', 'max:9'],
            'copper' => ['required', 'integer', 'min:0', 'max:9'],
            'note' => ['nullable', 'string', 'max:160'],
        ];
    }

    public function gold(): int
    {
        return $this->validated()->integer('gold');
    }

    public function silver(): int
    {
        return $this->validated()->integer('silver');
    }

    public function copper(): int
    {
        return $this->validated()->integer('copper');
    }

    public function note(): string
    {
        return $this->validated()->string('note');
    }
}
