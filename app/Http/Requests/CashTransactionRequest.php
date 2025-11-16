<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'type' => 'required|in:in,out',
            'cash_account_id' => 'required|exists:accounts,id',
            'contra_account_id' => 'required|exists:accounts,id|different:cash_account_id',
            'cashflow_category_id' => 'required|exists:cashflow_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:255',
            'reference' => 'nullable|string|max:100',
        ];
    }
    
    public function messages(): array
    {
        return [
            'date.required' => 'Tanggal wajib diisi',
            'type.required' => 'Tipe transaksi wajib dipilih',
            'cash_account_id.required' => 'Akun kas wajib dipilih',
            'contra_account_id.required' => 'Akun lawan wajib dipilih',
            'contra_account_id.different' => 'Akun lawan harus berbeda dengan akun kas',
            'amount.required' => 'Jumlah wajib diisi',
            'amount.min' => 'Jumlah minimal 0.01',
            'description.required' => 'Deskripsi wajib diisi',
        ];
    }
}