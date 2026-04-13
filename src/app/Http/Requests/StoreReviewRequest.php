<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'author_name' => ['required', 'string', 'max:255'],
            'author_city' => ['nullable', 'string', 'max:255'],
            'text' => ['required', 'string', 'max:5000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }
}
