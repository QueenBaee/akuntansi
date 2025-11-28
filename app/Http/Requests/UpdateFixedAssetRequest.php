<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFixedAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $assetId = $this->route('fixedAsset') ? $this->route('fixedAsset')->id : $this->route('fixed_asset');

        return [
            'code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('fixed_assets', 'code')->ignore($assetId)
            ],
            'name' => 'sometimes|string|max:255',
            'parent_id' => 'nullable|exists:fixed_assets,id',
            'acquisition_date' => 'sometimes|date',
            'acquisition_price' => 'sometimes|numeric|min:0.01',
            'residual_value' => 'nullable|numeric|min:0',
            'useful_life_months' => 'sometimes|integer|min:1|max:600',
            'asset_account_id' => 'sometimes|exists:trial_balances,id',
            'accumulated_account_id' => 'sometimes|exists:trial_balances,id',
            'expense_account_id' => 'sometimes|exists:trial_balances,id',
            'is_active' => 'sometimes|boolean',
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