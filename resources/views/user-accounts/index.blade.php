@extends('layouts.app')

@section('title', 'User Accounts')

@section('page-header')
<div class="page-pretitle">Management</div>
<h2 class="page-title">User Accounts</h2>
@endsection

@section('page-actions')
<a href="{{ route('user-accounts.create') }}" class="btn btn-primary">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
        <path stroke="none" d="m0 0h24v24H0z" fill="none"/>
        <line x1="12" y1="5" x2="12" y2="19"/>
        <line x1="5" y1="12" x2="19" y2="12"/>
    </svg>
    Add User Account
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">User Account List</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Account</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="w-1">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($userAccounts as $userAccount)
                            <tr>
                                <td>
                                    <div class="d-flex py-1 align-items-center">
                                        <span class="avatar me-2" style="background-image: url(https://ui-avatars.com/api/?name={{ urlencode($userAccount->user->name) }}&background=206bc4&color=fff)"></span>
                                        <div class="flex-fill">
                                            <div class="font-weight-medium">{{ $userAccount->user->name }}</div>
                                            <div class="text-muted">{{ $userAccount->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <div class="font-weight-medium">{{ $userAccount->account->name }}</div>
                                        <div class="text-muted">{{ $userAccount->account->code }} - {{ ucfirst($userAccount->account->type) }}</div>
                                    </div>
                                </td>
                                <td>
                                    @if($userAccount->role)
                                        <span class="badge bg-blue-lt">{{ $userAccount->role }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $userAccount->is_active ? 'bg-green-lt' : 'bg-red-lt' }}">
                                        {{ $userAccount->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-muted">
                                    {{ $userAccount->created_at->format('d M Y') }}
                                </td>
                                <td>
                                    <div class="btn-list flex-nowrap">
                                        <a href="{{ route('user-accounts.show', $userAccount) }}" class="btn btn-white btn-sm">View</a>
                                        <a href="{{ route('user-accounts.edit', $userAccount) }}" class="btn btn-white btn-sm">Edit</a>
                                        <form action="{{ route('user-accounts.destroy', $userAccount) }}" method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this user account?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-white btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No user accounts found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($userAccounts->hasPages())
                <div class="card-footer d-flex align-items-center">
                    <div class="text-muted">
                        Showing {{ $userAccounts->firstItem() }} to {{ $userAccounts->lastItem() }} of {{ $userAccounts->total() }} entries
                    </div>
                    <div class="ms-auto">
                        {{ $userAccounts->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection