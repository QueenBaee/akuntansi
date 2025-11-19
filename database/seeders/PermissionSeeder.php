<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User management
            'users.view',
            'users.create', 
            'users.update',
            'users.delete',
            
            // Role management
            'roles.view',
            'roles.create',
            'roles.update', 
            'roles.delete',
            
            // Account management
            'accounts.view',
            'accounts.create',
            'accounts.update',
            'accounts.delete',
            
            // Journal management
            'journals.view',
            'journals.create',
            'journals.update',
            'journals.delete',
            'journals.post',
            
            // Cash transactions
            'cash.view',
            'cash.create',
            'cash.update',
            'cash.delete',
            
            // Bank transactions
            'bank.view',
            'bank.create',
            'bank.update',
            'bank.delete',
            
            // Asset management
            'assets.view',
            'assets.create',
            'assets.update',
            'assets.delete',
            
            // Depreciation
            'depreciation.view',
            'depreciation.process',
            
            // Maklon
            'maklon.view',
            'maklon.create',
            'maklon.update',
            'maklon.delete',
            
            // Rent income
            'rent_income.view',
            'rent_income.create',
            'rent_income.update',
            'rent_income.delete',
            
            // Rent expense
            'rent_expense.view',
            'rent_expense.create',
            'rent_expense.update',
            'rent_expense.delete',
            
            // Reports
            'reports.view',
            'reports.export',
            
            // Dashboard
            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Admin can access all features
        $adminRole->givePermissionTo(Permission::all());

        // User can only access transactions, journals, and reports (no master data/configuration)
        $userRole->givePermissionTo([
            'journals.view', 'journals.create', 'journals.update', 'journals.post',
            'cash.view', 'cash.create', 'cash.update',
            'bank.view', 'bank.create', 'bank.update',
            'assets.view', 'assets.create', 'assets.update',
            'depreciation.view',
            'maklon.view', 'maklon.create', 'maklon.update',
            'rent_income.view', 'rent_income.create', 'rent_income.update',
            'rent_expense.view', 'rent_expense.create', 'rent_expense.update',
            'reports.view', 'reports.export',
            'dashboard.view',
        ]);
    }
}