<?php

use App\Helpers\AccountingHelper;

if (!function_exists('formatAccounting')) {
    function formatAccounting($value)
    {
        return AccountingHelper::formatAccounting($value);
    }
}