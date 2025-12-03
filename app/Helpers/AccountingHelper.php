<?php

namespace App\Helpers;

class AccountingHelper
{
    public static function formatAccounting($value)
    {
        if ($value == 0) {
            return '';
        }
        
        $formatted = number_format(abs($value), 0, ',', '.');
        
        return $value < 0 ? "($formatted)" : $formatted;
    }
}