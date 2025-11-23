<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLedgerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kode' => 'required|string|max:20|unique:ledgers,kode',
            'nama' => 'required|string|max:255',
            'tipe_akun' => 'required|in:aset,kewajiban,ekuitas,pendapatan,beban',
            'grup' => 'required|string|max:100',
            'saldo_normal' => 'required|in:debit,kredit'
        ];
    }

    public function messages(): array
    {
        return [
            'kode.required' => 'Kode harus diisi',
            'kode.unique' => 'Kode sudah digunakan',
            'nama.required' => 'Nama harus diisi',
            'tipe_akun.required' => 'Tipe akun harus dipilih',
            'grup.required' => 'Grup harus diisi',
            'saldo_normal.required' => 'Saldo normal harus dipilih'
        ];
    }
}