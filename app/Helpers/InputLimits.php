<?php

namespace App\Helpers;

class InputLimits
{
    public static function get($field)
    {
        $limits = [
            'date' => 10,
            'tanggal' => 10,
            'description' => 70,
            'keterangan' => 70,
            'pic' => 15,
            'reference' => 10,
            'no_bukti' => 10,
            'proof_number' => 10,
            'masuk' => 15,
            'keluar' => 15,
            'saldo' => 15,
            'balance' => 15,
            'akun_cf' => 50,
            'debit' => 50,
            'kredit' => 50,
            'credit' => 50,
            'debit_amount' => 15,
            'credit_amount' => 15,
            'cash_in' => 15,
            'cash_out' => 15,
        ];

        return $limits[strtolower($field)] ?? null;
    }

    public static function rules()
    {
        return [
            'date' => 'max:10',
            'tanggal' => 'max:10',
            'description' => 'max:70',
            'keterangan' => 'max:70',
            'pic' => 'max:15',
            'reference' => 'max:10',
            'no_bukti' => 'max:10',
            'proof_number' => 'max:10',
            'masuk' => 'max:15',
            'keluar' => 'max:15',
            'saldo' => 'max:15',
            'balance' => 'max:15',
            'akun_cf' => 'max:50',
            'debit' => 'max:50',
            'kredit' => 'max:50',
            'credit' => 'max:50',
            'debit_amount' => 'max:15',
            'credit_amount' => 'max:15',
            'cash_in' => 'max:15',
            'cash_out' => 'max:15',
        ];
    }
}