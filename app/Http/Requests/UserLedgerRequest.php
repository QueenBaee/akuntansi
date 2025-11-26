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
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'user_id' => 'required|exists:users,id',
            'ledger_id' => 'required|exists:ledgers,id',
            'role' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];

        // Add unique validation only for create or when user_id/ledger_id changes
        if (!$isUpdate || $userLedgerId === null) {
            $rules['user_id'][] = Rule::unique('user_ledgers')->where(function ($query) {
                return $query->where('ledger_id', $this->input('ledger_id'));
            });
        } else {
            $rules['user_id'][] = Rule::unique('user_ledgers')
                ->ignore($userLedgerId)
                ->where(function ($query) {
                    return $query->where('ledger_id', $this->input('ledger_id'));
                });
        }

        return $rules;
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