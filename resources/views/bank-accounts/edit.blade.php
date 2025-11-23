<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Edit Akun Bank - Sistem Akuntansi</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
</head>
<body>
    <div class="page">
        <div class="page-wrapper">
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title">Edit Akun Bank</h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="page-body">
                <div class="container-xl">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('bank-accounts.update', $bankAccount) }}">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label">Nama Akun</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $bankAccount->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nama Bank</label>
                                    <input type="text" class="form-control @error('bank_name') is-invalid @enderror" name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name) }}" required>
                                    @error('bank_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Nomor Rekening</label>
                                    <input type="text" class="form-control @error('account_number') is-invalid @enderror" name="account_number" value="{{ old('account_number', $bankAccount->account_number) }}" required>
                                    @error('account_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea class="form-control" name="description" rows="3">{{ old('description', $bankAccount->description) }}</textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-check">
                                        <input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active', $bankAccount->is_active) ? 'checked' : '' }}>
                                        <span class="form-check-label">Aktif</span>
                                    </label>
                                </div>
                                
                                <div class="form-footer">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                    <a href="{{ route('bank-accounts.index') }}" class="btn btn-secondary">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>