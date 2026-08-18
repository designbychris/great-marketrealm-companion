<?php

declare(strict_types=1);

namespace GreatMarketrealmCompanion\Modules\Parties\Requests;

use GreatMarketrealmCompanion\Core\Http\FormRequest;

defined('ABSPATH') || exit;

final class TransferPartyCoinRequest extends FormRequest
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
                'max:26',
            ],
            'transfer_id' => [
                'required',
                'string',
                'max:64',
            ],
            'direction' => [
                'required',
                'string',
                'in:to-treasury,to-character',
            ],
            'gold' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],
            'silver' => [
                'required',
                'integer',
                'min:0',
                'max:9',
            ],
            'copper' => [
                'required',
                'integer',
                'min:0',
                'max:9',
            ],
            'note' => [
                'nullable',
                'string',
                'max:120',
            ],
        ];
    }

    public function characterId(): string
    {
        return $this->validated()->string('character_id');
    }

    public function transferId(): string
    {
        return $this->validated()->string('transfer_id');
    }

    public function direction(): string
    {
        return $this->validated()->string('direction');
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
