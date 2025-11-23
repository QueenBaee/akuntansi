<?php

namespace App\Services;

use App\Models\CashAccount;
use App\Models\BankAccount;

class AccountService
{
    public static function getActiveCashAccounts()
    {
        return CashAccount::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public static function getActiveBankAccounts()
    {
        return BankAccount::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public static function getAllActiveAccounts()
    {
        return [
            'cash' => self::getActiveCashAccounts(),
            'bank' => self::getActiveBankAccounts()
        ];
    }
}