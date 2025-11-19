<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQrisSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'admin';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'remove_qris_image' => $this->has('remove_qris_image')
                ? filter_var($this->input('remove_qris_image'), FILTER_VALIDATE_BOOLEAN)
                : false,
        ]);
    }

    public function rules(): array
    {
        return [
            'qris_payload' => ['required', 'string', 'starts_with:000201'],
            'qris_image' => ['nullable', 'image', 'max:2048'],
            'remove_qris_image' => ['sometimes', 'boolean'],
        ];
    }
}
