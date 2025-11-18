<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userAccountId = $this->route('user_account')?->id;

        return [
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('user_accounts')->ignore($userAccountId)->where(function ($query) {
                    return $query->where('account_id', $this->account_id);
                }),
            ],
            'account_id' => [
                'required',
                'exists:accounts,id',
                Rule::unique('user_accounts')->ignore($userAccountId)->where(function ($query) {
                    return $query->where('user_id', $this->user_id);
                }),
            ],
            'role' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User is required.',
            'user_id.exists' => 'Selected user does not exist.',
            'user_id.unique' => 'This user-account combination already exists.',
            'account_id.required' => 'Account is required.',
            'account_id.exists' => 'Selected account does not exist.',
            'account_id.unique' => 'This user-account combination already exists.',
            'role.string' => 'Role must be a string.',
            'role.max' => 'Role cannot exceed 255 characters.',
            'is_active.boolean' => 'Status must be true or false.',
        ];
    }
}