@extends('layouts.app')

@section('title', 'Journal Entry')

@section('page-header')
    <div class="page-pretitle">Jurnal</div>
    <h2 class="page-title">Journal Entry</h2>
@endsection

@section('page-actions')
    <button class="btn btn-primary" onclick="showCreateForm()">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Jurnal
    </button>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Create Form -->
        <div class="card mb-3" id="create-form" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">Tambah Jurnal Baru</h3>
            </div>
            <form id="journal-form">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Referensi</label>
                            <input type="text" class="form-control" id="reference" name="reference" placeholder="Nomor bukti">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Deskripsi jurnal" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="addJournalLine()">+ Baris</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Akun</th>
                                    <th>Deskripsi</th>
                                    <th>Debit</th>
                                    <th>Kredit</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="journal-lines">
                                <!-- Lines will be added here -->
                            </tbody>
                            <tfoot>
                                <tr class="table-active">
                                    <td colspan="2"><strong>Total</strong></td>
                                    <td><strong id="total-debit">0</strong></td>
                                    <td><strong id="total-credit">0</strong></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-success me-2">Simpan</button>
                        <button type="button" class="btn btn-secondary" onclick="hideCreateForm()">Batal</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Journals List -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Jurnal</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Referensi</th>
                            <th>Deskripsi</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($journals ?? [] as $journal)
                        <tr>
                            <td>{{ $journal->date }}</td>
                            <td>{{ $journal->reference ?? '-' }}</td>
                            <td>{{ $journal->description }}</td>
                            <td>{{ number_format($journal->details->sum('debit'), 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-{{ $journal->is_posted ? 'success' : 'warning' }}">
                                    {{ $journal->is_posted ? 'Posted' : 'Draft' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-list flex-nowrap">
                                    <button class="btn btn-sm btn-outline-primary" onclick="viewJournal({{ $journal->id }})">
                                        View
                                    </button>
                                    @if(!$journal->is_posted)
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteJournal({{ $journal->id }})">
                                        Hapus
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Belum ada jurnal
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Alert Modal -->
<div class="modal fade" id="alertModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="alertModalTitle">Info</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="alertModalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Konfirmasi</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="confirmModalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmModalAction">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let lineIndex = 0;

function showCreateForm() {
    document.getElementById('create-form').style.display = 'block';
    addJournalLine();
    addJournalLine();
}

function hideCreateForm() {
    document.getElementById('create-form').style.display = 'none';
    document.getElementById('journal-form').reset();
    document.getElementById('journal-lines').innerHTML = '';
    lineIndex = 0;
}

const accountOptions = `
    <option value="">Pilih Akun</option>
    @foreach($accounts as $account)
        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
    @endforeach
`;

function addJournalLine() {
    const tbody = document.getElementById('journal-lines');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            <select name="details[${lineIndex}][account_id]" class="form-select" required>
                ${accountOptions}
            </select>
        </td>
        <td>
            <input type="text" name="details[${lineIndex}][description]" class="form-control" placeholder="Deskripsi">
        </td>
        <td>
            <input type="number" name="details[${lineIndex}][debit]" class="form-control debit-input" step="0.01" min="0" onchange="calculateTotals()">
        </td>
        <td>
            <input type="number" name="details[${lineIndex}][credit]" class="form-control credit-input" step="0.01" min="0" onchange="calculateTotals()">
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeJournalLine(this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="m0 0h24v24H0z" fill="none"/><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </td>
    `;
    tbody.appendChild(row);
    lineIndex++;
}

function removeJournalLine(button) {
    button.closest('tr').remove();
    calculateTotals();
}

function calculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;
    
    document.querySelectorAll('.debit-input').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });
    
    document.querySelectorAll('.credit-input').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });
    
    document.getElementById('total-debit').textContent = totalDebit.toLocaleString('id-ID');
    document.getElementById('total-credit').textContent = totalCredit.toLocaleString('id-ID');
}

document.getElementById('journal-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Validate totals
    let totalDebit = 0;
    let totalCredit = 0;
    
    document.querySelectorAll('.debit-input').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });
    
    document.querySelectorAll('.credit-input').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });
    
    if (totalDebit !== totalCredit) {
        showAlert('Error', 'Total debit harus sama dengan total kredit');
        return;
    }
    
    if (totalDebit === 0) {
        showAlert('Error', 'Total debit dan kredit tidak boleh 0');
        return;
    }
    
    const formData = new FormData(e.target);
    
    try {
        const response = await fetch('/journals', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (!response.ok) {
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat().join('\n');
                showAlert('Validation Error', errorMessages);
            } else {
                showAlert('Error', data.message || 'Terjadi kesalahan');
            }
            return;
        }
        
        if (data.success) {
            showAlert('Sukses', 'Jurnal berhasil disimpan');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('Error', data.message || 'Terjadi kesalahan');
        }
    } catch (error) {
        showAlert('Error', 'Terjadi kesalahan saat menyimpan data');
    }
});

async function deleteJournal(id) {
    showConfirm('Apakah Anda yakin ingin menghapus jurnal ini?', async () => {
        try {
            const response = await fetch(`/journals/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showAlert('Sukses', 'Jurnal berhasil dihapus', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showAlert('Error', data.message || 'Gagal menghapus jurnal', 'error');
            }
        } catch (error) {
            showAlert('Error', 'Terjadi kesalahan saat menghapus data', 'error');
        }
    });
}

function showAlert(title, message, type = 'info') {
    document.getElementById('alertModalTitle').textContent = title;
    document.getElementById('alertModalMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('alertModal'));
    modal.show();
}

function showConfirm(message, callback) {
    document.getElementById('confirmModalMessage').textContent = message;
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    document.getElementById('confirmModalAction').onclick = () => {
        modal.hide();
        callback();
    };
    modal.show();
}

function viewJournal(id) {
    // Implement view journal details
    alert('View journal ' + id);
}
</script>
@endpush