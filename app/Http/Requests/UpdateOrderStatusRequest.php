<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ensure kitchen and cashier status transitions are valid
class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:IN_PROGRESS,READY,SERVED,PAID,CLOSED'],
        ];
    }
}

