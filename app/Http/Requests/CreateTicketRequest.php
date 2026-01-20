<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'in:LOW,MEDIUM,HIGH,URGENT'],
            'category' => ['required', 'in:ACCOUNT_ACCESS,BILLING,TECHNICAL,FEATURE_REQUEST,OTHER'],
        ];
    }
}