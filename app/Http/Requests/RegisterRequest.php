<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'mobile' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers()],
        ];
    }

    protected function prepareForValidation()
    {
        // Remove role from request - users cannot set their own role
        $this->request->remove('role');
    }
}