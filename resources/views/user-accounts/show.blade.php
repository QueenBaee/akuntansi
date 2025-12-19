@extends('layouts.app')

@section('title', 'User Account Details')

@section('page-header')
<div class="page-pretitle">Management</div>
<h2 class="page-title">User Account Details</h2>
@endsection

@section('page-actions')
<div class="btn-list">
    <a href="{{ route('user-accounts.edit', $userAccount) }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
            <path d="M16 5l3 3"/>
        </svg>
        Edit
    </a>
    <form action="{{ route('user-accounts.destroy', $userAccount) }}" method="POST" style="display: inline;" 
          onsubmit="return confirm('Are you sure you want to delete this user account?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                <line x1="4" y1="7" x2="20" y2="7"/>
                <line x1="10" y1="11" x2="10" y2="17"/>
                <line x1="14" y1="11" x2="14" y2="17"/>
                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
            </svg>
            Delete
        </button>
    </form>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">User Account Information</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">User</label>
                            <div class="d-flex align-items-center">
                                <span class="avatar me-2" style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($userAccount->user?->name ?? 'User') }}&background=206bc4&color=fff)"></span>
                                <div>
                                    <div class="font-weight-medium">{{ $userAccount->user?->name ?? '-' }}</div>
                                    <div class="text-muted">{{ $userAccount->user?->email ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Account</label>
                            <div>
                                <div class="font-weight-medium">{{ $userAccount->account?->name ?? '-' }}</div>
                                <div class="text-muted">{{ $userAccount->account?->code ?? '-' }} - {{ ucfirst($userAccount->account?->type ?? '-') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <div>
                                @if($userAccount->role)
                                    <span class="badge bg-blue-lt">{{ $userAccount->role }}</span>
                                @else
                                    <span class="text-muted">No role assigned</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div>
                                <span class="badge {{ $userAccount->is_active ? 'bg-green-lt' : 'bg-red-lt' }}">
                                    {{ $userAccount->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Created At</label>
                            <div class="text-muted">{{ $userAccount->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Updated At</label>
                            <div class="text-muted">{{ $userAccount->updated_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('user-accounts.index') }}" class="btn btn-link">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <line x1="5" y1="12" x2="11" y2="6"/>
                        <line x1="5" y1="12" x2="11" y2="18"/>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection