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
        $fixedAsset = $this->route('fixedAsset');
        
        return [
            'code' => [
                'required',
                'string',
                'max:50',
                \Illuminate\Validation\Rule::unique('fixed_assets', 'code')->ignore($fixedAsset),
            ],
            'name' => 'required|string|max:255',
            'quantity' => 'nullable|integer|min:1',
            'location' => 'nullable|string|max:255',
            'group' => 'required|in:Permanent,Non-permanent,Group 1,Group 2',
            'condition' => 'required|in:Baik,Rusak',
            'status' => 'required|in:active,inactive',
            'parent_id' => 'nullable|exists:fixed_assets,id',
            'acquisition_date' => 'required|date',
            'acquisition_price' => 'required|numeric|min:0.01',
            'residual_value' => 'nullable|numeric|min:0',
            'depreciation_method' => 'required|in:garis lurus,saldo menurun',
            'useful_life_years' => 'required|integer|min:1|max:50',
            'useful_life_months' => 'nullable|integer|min:1|max:600',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'depreciation_start_date' => 'required|date',
            'asset_account_id' => 'required|exists:trial_balances,id',
            'accumulated_account_id' => 'required|exists:trial_balances,id',
            'expense_account_id' => 'required|exists:trial_balances,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Kode aset wajib diisi.',
            'code.unique' => 'Kode aset sudah digunakan.',
            'name.required' => 'Nama aset wajib diisi.',
            'group.required' => 'Grup aset wajib dipilih.',
            'group.in' => 'Grup aset tidak valid. Pilih: Permanent, Non-permanent, Group 1, atau Group 2.',
            'condition.required' => 'Kondisi aset wajib dipilih.',
            'condition.in' => 'Kondisi aset tidak valid. Pilih: Baik atau Rusak.',
            'status.required' => 'Status aset wajib dipilih.',
            'status.in' => 'Status aset tidak valid. Pilih: active atau inactive.',
            'depreciation_method.required' => 'Metode penyusutan wajib dipilih.',
            'depreciation_method.in' => 'Metode penyusutan tidak valid. Pilih: garis lurus atau saldo menurun.',
            'acquisition_price.min' => 'Harga perolehan harus lebih dari 0.',
            'useful_life_years.min' => 'Umur manfaat minimal 1 tahun.',
            'useful_life_years.max' => 'Umur manfaat maksimal 50 tahun.',
            'depreciation_start_date.required' => 'Tanggal mulai penyusutan wajib diisi.',
        ];
    }

    protected function prepareForValidation()
    {
        $mergeData = [];

        // Map English values to Indonesian database values
        if ($this->condition === 'Good') {
            $mergeData['condition'] = 'Baik';
        } elseif ($this->condition === 'Damaged') {
            $mergeData['condition'] = 'Rusak';
        }

        if ($this->status === 'Active') {
            $mergeData['status'] = 'active';
        } elseif ($this->status === 'Inactive') {
            $mergeData['status'] = 'inactive';
        }

        if ($this->depreciation_method === 'Straight Line') {
            $mergeData['depreciation_method'] = 'garis lurus';
        } elseif ($this->depreciation_method === 'Declining Balance') {
            $mergeData['depreciation_method'] = 'saldo menurun';
        }

        // Set default quantity
        if (!$this->quantity) {
            $mergeData['quantity'] = 1;
        }

        // Set default residual value
        if (!$this->residual_value) {
            $mergeData['residual_value'] = 1;
        }

        // Auto-calculate useful life in months
        if ($this->useful_life_years && !$this->useful_life_months) {
            $mergeData['useful_life_months'] = $this->useful_life_years * 12;
        }

        // Auto-calculate depreciation rate
        if ($this->depreciation_method && $this->useful_life_years && !$this->depreciation_rate) {
            $rate = 0;
            $method = $mergeData['depreciation_method'] ?? $this->depreciation_method;
            if ($method === 'garis lurus') {
                $rate = round(100 / $this->useful_life_years, 2);
            } elseif ($method === 'saldo menurun') {
                $rate = round((2 / $this->useful_life_years) * 100, 2);
            }
            $mergeData['depreciation_rate'] = $rate;
        }

        if (!empty($mergeData)) {
            $this->merge($mergeData);
        }
    }
}