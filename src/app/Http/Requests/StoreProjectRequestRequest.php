<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'contact' => ['required', 'string', 'max:255'],
            'task' => ['required', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,zip', 'max:10240'],
        ];
    }
}
