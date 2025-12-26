<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFixedAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:fixed_assets,code',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:fixed_assets,id',
            'acquisition_date' => 'required|date',
            'acquisition_price' => 'required|numeric|min:0.01',
            'residual_value' => 'nullable|numeric|min:0|lt:acquisition_price',
            'useful_life_months' => 'required|integer|min:1|max:600',
            'asset_account_id' => 'required|exists:trial_balances,id',
            'accumulated_account_id' => 'required|exists:trial_balances,id',
            'expense_account_id' => 'required|exists:trial_balances,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique' => 'Kode aset sudah digunakan.',
            'acquisition_price.min' => 'Harga perolehan harus lebih dari 0.',
            'residual_value.lt' => 'Nilai residual harus lebih kecil dari harga perolehan.',
            'useful_life_months.min' => 'Umur manfaat minimal 1 bulan.',
            'useful_life_months.max' => 'Umur manfaat maksimal 600 bulan (50 tahun).',
        ];
    }
}