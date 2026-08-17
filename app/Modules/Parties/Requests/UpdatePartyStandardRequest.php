<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class UpdatePartyStandardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return is_user_logged_in();
    }

    public function rules(): array
    {
        return [
            'palette' => [
                'required',
                'string',
                'in:aubergine-gold,pantry-green,frost-blue,berry-red,cheddar-gold',
            ],
            'emblem' => [
                'required',
                'string',
                'in:guild-star,market-leaf,company-crown,adventurers-cross,guild-cart',
            ],
            'ornament' => [
                'required',
                'string',
                'in:flourish,laurels,stars,diamond,plain',
            ],
        ];
    }

    public function palette(): string
    {
        return $this->validated()->string('palette');
    }

    public function emblem(): string
    {
        return $this->validated()->string('emblem');
    }

    public function ornament(): string
    {
        return $this->validated()->string('ornament');
    }
}
