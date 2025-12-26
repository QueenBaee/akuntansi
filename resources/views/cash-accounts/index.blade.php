<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Akun Kas - Sistem Akuntansi</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
</head>
<body>
    <div class="page">
        <div class="page-wrapper">
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title">Akun Kas</h2>
                        </div>
                        <div class="col-auto ms-auto d-print-none">
                            <a href="{{ route('cash-accounts.create') }}" class="btn btn-primary">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Tambah Akun Kas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="page-body">
                <div class="container-xl">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <div class="card">
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Nomor Akun</th>
                                        <th>Akun CF</th>
                                        <th>Deskripsi</th>
                                        <th>Status</th>
                                        <th class="w-1">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cashAccounts as $account)
                                    <tr>
                                        <td>{{ $account->name }}</td>
                                        <td>{{ $account->account_number }}</td>
                                        <td>
                                            @if($account->cashflow)
                                                <span class="badge bg-info">{{ $account->cashflow->kode }}</span><br>
                                                <small class="text-muted">{{ $account->cashflow->keterangan }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $account->description }}</td>
                                        <td>
                                            <span class="badge bg-{{ $account->is_active ? 'success' : 'danger' }}">
                                                {{ $account->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-list flex-nowrap">
                                                <a href="{{ route('cash-accounts.edit', $account) }}" class="btn btn-sm">Edit</a>
                                                <form method="POST" action="{{ route('cash-accounts.destroy', $account) }}" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>