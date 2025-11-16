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
            'type.in' => 'Tipe transaksi harus in atau out',
            'cash_account_id.required' => 'Akun kas wajib dipilih',
            'cash_account_id.exists' => 'Akun kas tidak valid',
            'contra_account_id.required' => 'Akun lawan wajib dipilih',
            'contra_account_id.exists' => 'Akun lawan tidak valid',
            'contra_account_id.different' => 'Akun lawan harus berbeda dengan akun kas',
            'cashflow_category_id.required' => 'Kategori arus kas wajib dipilih',
            'cashflow_category_id.exists' => 'Kategori arus kas tidak valid',
            'amount.required' => 'Jumlah wajib diisi',
            'amount.numeric' => 'Jumlah harus berupa angka',
            'amount.min' => 'Jumlah minimal 0.01',
            'description.required' => 'Deskripsi wajib diisi',
            'description.max' => 'Deskripsi maksimal 255 karakter',
            'reference.max' => 'Referensi maksimal 100 karakter',
        ];
    }
}