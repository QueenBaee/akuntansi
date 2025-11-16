<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JournalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'reference' => 'nullable|string|max:100',
            'details' => 'required|array|min:2',
            'details.*.account_id' => 'required|exists:accounts,id',
            'details.*.debit' => 'required|numeric|min:0',
            'details.*.credit' => 'required|numeric|min:0',
            'details.*.description' => 'nullable|string|max:255',
        ];
    }
    
    public function messages(): array
    {
        return [
            'date.required' => 'Tanggal wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'details.required' => 'Detail jurnal wajib diisi',
            'details.min' => 'Minimal 2 baris detail jurnal',
            'details.*.account_id.required' => 'Akun wajib dipilih',
            'details.*.debit.required' => 'Debit wajib diisi',
            'details.*.credit.required' => 'Kredit wajib diisi',
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $details = $this->input('details', []);
            $totalDebit = collect($details)->sum('debit');
            $totalCredit = collect($details)->sum('credit');
            
            if ($totalDebit != $totalCredit) {
                $validator->errors()->add('details', 'Total debit harus sama dengan total kredit');
            }
            
            // Check if each line has either debit or credit (not both)
            foreach ($details as $index => $detail) {
                $debit = floatval($detail['debit'] ?? 0);
                $credit = floatval($detail['credit'] ?? 0);
                
                if ($debit > 0 && $credit > 0) {
                    $validator->errors()->add("details.{$index}", 'Baris tidak boleh memiliki debit dan kredit sekaligus');
                }
                
                if ($debit == 0 && $credit == 0) {
                    $validator->errors()->add("details.{$index}", 'Baris harus memiliki debit atau kredit');
                }
            }
        });
    }
}