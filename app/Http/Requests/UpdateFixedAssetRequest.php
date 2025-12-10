<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\FixedAsset;
use App\Models\TrialBalance;

class UpdateFixedAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $assetId = $this->route('fixed_asset') ? $this->route('fixed_asset')->id : $this->route('fixedAsset');
        
        return [
            'asset_number' => 'nullable|string|max:50|unique:fixed_assets,asset_number,' . $assetId,
            'asset_name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:fixed_assets,code,' . $assetId,
            'name' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:255',
            'group' => 'required|in:Permanent,Non-permanent,Group 1,Group 2',
            'condition' => 'required|in:Good,Damaged',
            'status' => 'required|in:Active,Inactive',
            'parent_id' => 'nullable|exists:fixed_assets,id',
            'acquisition_date' => 'required|date',
            'acquisition_price' => 'required|numeric|min:0.01',
            'residual_value' => 'nullable|numeric|min:0',
            'depreciation_method' => 'required|in:Straight Line,Declining Balance',
            'useful_life_years' => 'required|integer|min:1|max:50',
            'useful_life_months' => 'nullable|integer|min:1|max:600',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'depreciation_start_date' => 'required|date',
            'account_acquisition' => 'nullable|string|max:20',
            'account_accumulated' => 'nullable|string|max:20',
            'account_expense' => 'nullable|string|max:20',
            'asset_account_id' => 'required|exists:trial_balances,id',
            'accumulated_account_id' => 'required|exists:trial_balances,id',
            'expense_account_id' => 'required|exists:trial_balances,id',
        ];
    }

    public function messages(): array
    {
        return [
            'asset_number.unique' => 'Nomor aset sudah digunakan.',
            'asset_name.required' => 'Nama aset wajib diisi.',
            'group.required' => 'Grup aset wajib dipilih.',
            'group.in' => 'Grup aset tidak valid.',
            'condition.required' => 'Kondisi aset wajib dipilih.',
            'condition.in' => 'Kondisi aset tidak valid.',
            'status.required' => 'Status aset wajib dipilih.',
            'status.in' => 'Status aset tidak valid.',
            'depreciation_method.required' => 'Metode penyusutan wajib dipilih.',
            'depreciation_method.in' => 'Metode penyusutan tidak valid.',
            'acquisition_price.min' => 'Harga perolehan harus lebih dari 0.',
            'useful_life_years.min' => 'Umur manfaat minimal 1 tahun.',
            'useful_life_years.max' => 'Umur manfaat maksimal 50 tahun.',
            'depreciation_start_date.required' => 'Tanggal mulai penyusutan wajib diisi.',
        ];
    }

    protected function prepareForValidation()
    {
        // Set default quantity
        if (!$this->quantity) {
            $this->merge(['quantity' => 1]);
        }

        // Set default residual value
        if (!$this->residual_value) {
            $this->merge(['residual_value' => 1]);
        }

        // Auto-calculate useful life in months
        if ($this->useful_life_years && !$this->useful_life_months) {
            $this->merge([
                'useful_life_months' => $this->useful_life_years * 12
            ]);
        }

        // Auto-calculate depreciation rate
        if ($this->depreciation_method && $this->useful_life_years && !$this->depreciation_rate) {
            $rate = 0;
            if ($this->depreciation_method === 'Straight Line') {
                $rate = round(100 / $this->useful_life_years, 2);
            } elseif ($this->depreciation_method === 'Declining Balance') {
                $rate = round((2 / $this->useful_life_years) * 100, 2);
            }
            $this->merge(['depreciation_rate' => $rate]);
        }
    }
}