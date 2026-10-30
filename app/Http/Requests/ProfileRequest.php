<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        if ($this->routeIs('profile.password')) {
            return [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', 'confirmed', Password::min(8)],
            ];
        }
        $newAdmin = $this->routeIs('profile.admins.store');
        $emailRule = Rule::unique('users', 'email');
        if (! $newAdmin) {
            $emailRule->ignore($this->user()->id);
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', $emailRule],
        ] + ($newAdmin ? ['password' => ['required', 'confirmed', Password::min(8)]] : []);
    }
}
