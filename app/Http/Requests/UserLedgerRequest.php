<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserLedgerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userLedgerId = $this->route('user_ledger') ? $this->route('user_ledger')->id : null;

        return [
            'user_id' => [
                'required',
                'exists:users,id',
                Rule::unique('user_ledgers')->ignore($userLedgerId)->where(function ($query) {
                    return $query->where('ledger_id', $this->ledger_id);
                }),
            ],
            'ledger_id' => [
                'required',
                'exists:ledgers,id',
            ],
            'role' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.unique' => 'This user is already assigned to this ledger.',
            'user_id.exists' => 'Selected user does not exist.',
            'ledger_id.exists' => 'Selected ledger does not exist.',
        ];
    }
}