<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
     public function authorize(): bool
    {
        return $this->user()->can('edit users') ||
               $this->user()->id === $this->route('user')->id;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($userId)],
            'password' => ['required', 'string', 'min:8'],
            'bio' => ['nullable', 'string', 'max:500'],
            'profile_photo' => ['nullable', 'string'],
            'status' => ['required', 'boolean'],
            'role' => ['required', Rule::in(['admin', 'author'])],
        ];
    }
}
