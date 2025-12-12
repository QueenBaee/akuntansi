<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\FixedAsset;
use App\Models\TrialBalance;

class StoreFixedAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        // Auto-generate asset number if not provided
        if (!$this->asset_number) {
            $this->merge([
                'asset_number' => FixedAsset::generateAssetNumber()
            ]);
        }

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

        // Auto-suggest account codes based on group
        if ($this->group && !$this->account_acquisition) {
            $accounts = $this->suggestAccountsByGroup($this->group);
            $this->merge([
                'account_acquisition' => $accounts['acquisition'],
                'account_accumulated' => $accounts['accumulated'],
                'account_expense' => $accounts['expense']
            ]);
        }
    }

    private function suggestAccountsByGroup($group)
    {
        switch ($group) {
            case 'Permanent':
                return [
                    'acquisition' => 'A23-01',
                    'accumulated' => 'A24-01',
                    'expense' => 'E22-96'
                ];
            case 'Non-permanent':
                return [
                    'acquisition' => 'A23-02',
                    'accumulated' => 'A24-02',
                    'expense' => 'E22-97'
                ];
            case 'Group 1':
                return [
                    'acquisition' => 'A23-03',
                    'accumulated' => 'A24-03',
                    'expense' => 'E22-98'
                ];
            case 'Group 2':
                return [
                    'acquisition' => 'A23-04',
                    'accumulated' => 'A24-04',
                    'expense' => 'E22-99'
                ];
            default:
                return [
                    'acquisition' => 'A23-01',
                    'accumulated' => 'A24-01',
                    'expense' => 'E22-96'
                ];
        }
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:fixed_assets,code',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'location' => 'nullable|string|max:255',
            'group' => 'required|in:Permanent,Non-permanent,Group 1,Group 2',
            'condition' => 'required|in:Baik,Rusak',
            'status' => 'required|in:active,inactive',
            'acquisition_date' => 'required|date',
            'acquisition_price' => 'required|numeric|min:0.01',
            'residual_value' => 'nullable|numeric|min:0',
            'depreciation_method' => 'required|in:garis lurus,saldo menurun',
            'useful_life_years' => 'nullable|integer|min:1|max:50',
            'useful_life_months' => 'nullable|integer|min:1|max:600',
            'depreciation_rate' => 'nullable|string|max:10',
            'depreciation_start_date' => 'required|date',
            'asset_account_id' => 'required|exists:trial_balances,id',
            'accumulated_account_id' => 'nullable|exists:trial_balances,id',
            'expense_account_id' => 'nullable|exists:trial_balances,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode aset wajib diisi.',
            'code.unique' => 'Kode aset sudah digunakan.',
            'name.required' => 'Nama aset wajib diisi.',
            'quantity.required' => 'Jumlah unit wajib diisi.',
            'quantity.min' => 'Jumlah unit minimal 1.',
            'group.required' => 'Kelompok aset wajib dipilih.',
            'group.in' => 'Kelompok aset tidak valid.',
            'condition.required' => 'Kondisi aset wajib dipilih.',
            'condition.in' => 'Kondisi aset tidak valid.',
            'status.required' => 'Status aset wajib dipilih.',
            'status.in' => 'Status aset tidak valid.',
            'depreciation_method.required' => 'Metode penyusutan wajib dipilih.',
            'depreciation_method.in' => 'Metode penyusutan tidak valid.',
            'acquisition_price.min' => 'Harga perolehan harus lebih dari 0.',
            'depreciation_start_date.required' => 'Tanggal mulai penyusutan wajib diisi.',
            'asset_account_id.required' => 'Akun harga perolehan wajib dipilih.',
        ];
    }
}