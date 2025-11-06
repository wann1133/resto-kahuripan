<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Validate customer order submission payload
class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_code' => ['required', 'string', 'exists:tables,code'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer', 'exists:menus,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.options' => ['nullable', 'array'],
            'items.*.options.*.id' => ['integer', 'exists:menu_options,id'],
            'items.*.notes' => ['nullable', 'string', 'max:255'],
            'payment.method' => ['required', 'string', 'in:CASH,QRIS'],
        ];
    }
}

