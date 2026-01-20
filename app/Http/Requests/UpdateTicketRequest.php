<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        // return auth()->user()->isAdmin();

        $user = $this->user();

        return $user && $user->isAdmin();
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'in:OPEN,IN_PROGRESS,RESOLVED,CLOSED'],
            'assigned_to' => ['sometimes', 'email', 'exists:users,email'],
            'internal_note' => ['sometimes', 'string'],
        ];
    }
}