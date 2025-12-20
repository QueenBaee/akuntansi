<?php

namespace App\Helpers;

class AssetGroupHelper
{
    /**
     * Translate asset group from English to Indonesian for display
     */
    public static function translateGroup($group)
    {
        $translations = [
            'Permanent' => 'Aset Permanen',
            'Non-permanent' => 'Aset Tidak Permanen', 
            'Group 1' => 'Kelompok 1',
            'Group 2' => 'Kelompok 2',
            'Aset Dalam Penyelesaian' => 'Aset Dalam Penyelesaian'
        ];

        return $translations[$group] ?? $group;
    }

    /**
     * Get all available groups with translations
     */
    public static function getAllGroups()
    {
        return [
            'Permanent' => 'Aset Permanen',
            'Non-permanent' => 'Aset Tidak Permanen',
            'Group 1' => 'Kelompok 1', 
            'Group 2' => 'Kelompok 2'
        ];
    }
}