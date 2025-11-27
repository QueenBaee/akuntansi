<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class PostDepreciationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period' => [
                'required',
                'date_format:Y-m-d',
                function ($attribute, $value, $fail) {
                    $period = Carbon::parse($value);
                    if ($period->day !== 1) {
                        $fail('Period harus tanggal 1 dari bulan yang dipilih.');
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'period.required' => 'Period harus diisi.',
            'period.date_format' => 'Format period tidak valid (Y-m-d).',
        ];
    }
}