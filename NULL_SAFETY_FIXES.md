# Null-Safety Fixes for 504 Gateway Timeout

## Problem
Blade views were accessing Eloquent relationships directly without null-safety checks, causing fatal errors and PHP-FPM timeouts when related models were missing, resulting in 504 Gateway Timeout errors.

## Solution
Applied Laravel's safe navigation operator (?->) and null coalescing operator (??) to all relationship access in Blade views.

## Files Fixed

### 1. resources/views/user-accounts/index.blade.php
**Changes:**
- `$userAccount->user->name` → `$userAccount->user?->name ?? '-'`
- `$userAccount->user->email` → `$userAccount->user?->email ?? '-'`
- `urlencode($userAccount->user->name)` → `urlencode($userAccount->user?->name ?? 'User')`
- `$userAccount->account->name` → `$userAccount->account?->name ?? '-'`
- `$userAccount->account->code` → `$userAccount->account?->code ?? '-'`
- `$userAccount->account->type` → `$userAccount->account?->type ?? '-'`

### 2. resources/views/user-accounts/show.blade.php
**Changes:**
- `$userAccount->user->name` → `$userAccount->user?->name ?? '-'`
- `$userAccount->user->email` → `$userAccount->user?->email ?? '-'`
- `urlencode($userAccount->user->name)` → `urlencode($userAccount->user?->name ?? 'User')`
- `$userAccount->account->name` → `$userAccount->account?->name ?? '-'`
- `$userAccount->account->code` → `$userAccount->account?->code ?? '-'`
- `$userAccount->account->type` → `$userAccount->account?->type ?? '-'`

### 3. resources/views/layouts/app.blade.php
**Changes:**
- `auth()->user()->name` → `auth()->user()?->name ?? 'User'`
- `auth()->user()->email` → `auth()->user()?->email ?? '-'`
- `urlencode(auth()->user()->name)` → `urlencode(auth()->user()?->name ?? 'User')`

## Benefits
✅ Prevents fatal errors when relationships are null
✅ Prevents PHP-FPM worker crashes
✅ Eliminates 504 Gateway Timeout errors
✅ Graceful fallback with '-' or 'User' when data is missing
✅ No changes to CSS or styling
✅ No controller or model modifications needed

## Testing
After applying these fixes:
1. Test pages with missing user relationships
2. Test pages with missing account relationships
3. Verify no 504 errors occur
4. Confirm UI displays '-' or 'User' for missing data
