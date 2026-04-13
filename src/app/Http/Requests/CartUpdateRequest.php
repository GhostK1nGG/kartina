<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'painting_id' => ['required', 'integer', 'exists:paintings,id'],
            'quantity' => ['required', 'integer', 'min:0', 'max:99'],
        ];
    }
}
