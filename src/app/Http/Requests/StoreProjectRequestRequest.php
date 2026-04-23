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

    public function messages(): array
    {
        return [
            'attachment.uploaded' => 'Не удалось загрузить вложение. Обычно это происходит, если файл слишком большой, повреждён или загрузка оборвалась. Попробуйте файл JPG, PNG, PDF или ZIP размером до 10 МБ.',
            'attachment.mimes' => 'Вложение должно быть в формате JPG, JPEG, PNG, PDF или ZIP.',
            'attachment.max' => 'Размер вложения не должен превышать 10 МБ.',
        ];
    }
}
