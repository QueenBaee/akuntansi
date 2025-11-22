<?php $__env->startSection('title', 'Buku Besar Per Akun'); ?>

<?php $__env->startSection('page-header'); ?>
    <div class="page-pretitle">Laporan</div>
    <h2 class="page-title">Buku Besar Per Akun</h2>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div id="ledger-app">
    <!-- Account Selection & Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label">Pilih Akun</label>
                    <select id="account-select" class="form-select">
                        <option value="">Pilih Akun</option>
                        <?php $__currentLoopData = $accounts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $account): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($account->id); ?>"><?php echo e($account->code); ?> - <?php echo e($account->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Mulai</label>
                    <input type="date" id="start-date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" id="end-date" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button id="filter-btn" class="btn btn-primary w-100">
                        Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Form -->
    <div id="create-form" class="card mb-4" style="display: none;">
        <div class="card-header">
            <h3 class="card-title">Tambah Entry Buku Besar</h3>
        </div>
        <div class="card-body">
            <form id="ledger-form" class="row">
                <div class="col-md-2">
                    <label class="form-label">Tanggal</label>
                    <input type="date" id="date" required class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Debit</label>
                    <input type="text" id="debit" placeholder="0" class="form-control currency-input">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kredit</label>
                    <input type="text" id="credit" placeholder="0" class="form-control currency-input">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Deskripsi</label>
                    <input type="text" id="description" placeholder="Deskripsi transaksi" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-success w-100">
                        Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title">Buku Besar</h3>
            <button id="add-entry-btn" class="btn btn-primary" style="display: none;">
                Tambah Entry
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-vcenter">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                        <th class="text-end">Debit</th>
                        <th class="text-end">Kredit</th>
                        <th class="text-end">Saldo</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="ledger-table">
                    <tr id="no-data-row">
                        <td colspan="6" class="text-center text-muted">Pilih akun untuk melihat buku besar</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $__env->startPush('styles'); ?>
<style>
.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}
.edit-mode {
    width: 100%;
}
.view-mode {
    display: block;
    min-height: 20px;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
class LedgerApp {
    constructor() {
        this.currentAccountId = null;
        this.ledgerData = [];
        this.init();
    }

    init() {
        this.bindEvents();
        this.setDefaultDates();
    }

    bindEvents() {
        // Currency input formatting
        document.addEventListener('input', (e) => {
            if (e.target.classList.contains('currency-input') || e.target.classList.contains('currency-cell')) {
                this.formatCurrencyInput(e.target);
            }
        });

        // Account selection
        document.getElementById('account-select').addEventListener('change', (e) => {
            this.currentAccountId = e.target.value;
            if (this.currentAccountId) {
                document.getElementById('add-entry-btn').style.display = 'block';
                this.fetchLedger();
            } else {
                document.getElementById('add-entry-btn').style.display = 'none';
                document.getElementById('create-form').style.display = 'none';
                this.showNoData();
            }
        });

        // Filter button
        document.getElementById('filter-btn').addEventListener('click', () => {
            if (this.currentAccountId) {
                this.fetchLedger();
            }
        });

        // Add entry button
        document.getElementById('add-entry-btn').addEventListener('click', () => {
            const form = document.getElementById('create-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        });

        // Create form
        document.getElementById('ledger-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.createLedgerEntry();
        });

        // Table events (edit mode buttons)
        document.getElementById('ledger-table').addEventListener('click', (e) => {
            const id = e.target.dataset.id;
            
            if (e.target.classList.contains('edit-btn')) {
                this.enterEditMode(id);
            } else if (e.target.classList.contains('save-btn')) {
                this.saveEdit(id);
            } else if (e.target.classList.contains('cancel-btn')) {
                this.cancelEdit(id);
            } else if (e.target.classList.contains('delete-btn')) {
                this.deleteLedgerEntry(id);
            }
        });
    }

    setDefaultDates() {
        const today = new Date();
        const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        
        document.getElementById('start-date').value = firstDay.toISOString().split('T')[0];
        document.getElementById('end-date').value = today.toISOString().split('T')[0];
    }

    async fetchLedger() {
        try {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            
            let url = `/api/ledgers/${this.currentAccountId}`;
            if (startDate && endDate) {
                url += `?start_date=${startDate}&end_date=${endDate}`;
            }

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) throw new Error('Failed to fetch ledger');

            this.ledgerData = await response.json();
            this.renderTable();
        } catch (error) {
            console.error('Error fetching ledger:', error);
            Swal.fire('Error', 'Gagal memuat data buku besar', 'error');
        }
    }

    renderTable() {
        const tbody = document.getElementById('ledger-table');
        tbody.innerHTML = '';

        if (this.ledgerData.length === 0) {
            this.showNoData();
            return;
        }

        this.ledgerData.forEach(ledger => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <span class="view-mode" data-field="date">${ledger.date}</span>
                    <input type="date" class="edit-mode form-control form-control-sm" data-field="date" value="${ledger.date}" style="display: none;">
                </td>
                <td>
                    <span class="view-mode" data-field="description">${ledger.description || ''}</span>
                    <input type="text" class="edit-mode form-control form-control-sm" data-field="description" value="${ledger.description || ''}" style="display: none;">
                </td>
                <td class="text-end">
                    <span class="view-mode" data-field="debit">${this.formatNumber(ledger.debit)}</span>
                    <input type="text" class="edit-mode form-control form-control-sm currency-input" data-field="debit" value="${this.formatNumber(ledger.debit)}" style="display: none;">
                </td>
                <td class="text-end">
                    <span class="view-mode" data-field="credit">${this.formatNumber(ledger.credit)}</span>
                    <input type="text" class="edit-mode form-control form-control-sm currency-input" data-field="credit" value="${this.formatNumber(ledger.credit)}" style="display: none;">
                </td>
                <td class="text-end fw-bold">
                    ${this.formatNumber(ledger.running_balance)}
                </td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button data-id="${ledger.id}" class="edit-btn btn btn-outline-primary" title="Edit entry">
                            Edit
                        </button>
                        <button data-id="${ledger.id}" class="save-btn btn btn-success" style="display: none;" title="Simpan perubahan">
                            Simpan
                        </button>
                        <button data-id="${ledger.id}" class="cancel-btn btn btn-secondary" style="display: none;" title="Batal edit">
                            Batal
                        </button>
                        <button data-id="${ledger.id}" class="delete-btn btn btn-outline-danger" title="Hapus entry">
                            Hapus
                        </button>
                    </div>
                </td>
            `;
            row.dataset.id = ledger.id;
            tbody.appendChild(row);
        });
    }

    showNoData() {
        const tbody = document.getElementById('ledger-table');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>';
    }

    async createLedgerEntry() {
        try {
            const data = {
                account_id: this.currentAccountId,
                date: document.getElementById('date').value,
                debit: this.parseCurrency(document.getElementById('debit').value) || 0,
                credit: this.parseCurrency(document.getElementById('credit').value) || 0,
                description: document.getElementById('description').value
            };

            // Validation
            if (!data.date) {
                Swal.fire('Error', 'Tanggal harus diisi', 'error');
                return;
            }

            if (parseFloat(data.debit) === 0 && parseFloat(data.credit) === 0) {
                Swal.fire('Error', 'Debit atau kredit harus diisi', 'error');
                return;
            }

            const response = await fetch('/api/ledgers', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to create ledger entry');
            }

            const result = await response.json();
            console.log('POST result:', result);
            
            // Reset form and hide it
            document.getElementById('ledger-form').reset();
            document.getElementById('create-form').style.display = 'none';
            
            // Refresh ledger data immediately
            console.log('Fetching updated ledger data...');
            await this.fetchLedger();
            console.log('Ledger data after refresh:', this.ledgerData);
            
            Swal.fire('Berhasil', 'Entry buku besar berhasil ditambahkan', 'success');
        } catch (error) {
            console.error('Error creating ledger entry:', error);
            Swal.fire('Error', error.message || 'Gagal menambahkan entry', 'error');
        }
    }

    enterEditMode(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (!row) return;
        
        // Hide view mode, show edit mode
        row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
        row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'block');
        
        // Hide edit button, show save/cancel buttons
        row.querySelector('.edit-btn').style.display = 'none';
        row.querySelector('.save-btn').style.display = 'inline-block';
        row.querySelector('.cancel-btn').style.display = 'inline-block';
        row.querySelector('.delete-btn').style.display = 'none';
    }
    
    async saveEdit(id) {
        try {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (!row) return;
            
            const data = {};
            row.querySelectorAll('.edit-mode').forEach(input => {
                const field = input.dataset.field;
                let value = input.value.trim();
                
                if (field === 'debit' || field === 'credit') {
                    value = this.parseCurrency(value);
                }
                
                data[field] = value;
            });
            
            const response = await fetch(`/api/ledgers/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });
            
            if (!response.ok) {
                throw new Error('Failed to update ledger entry');
            }
            
            await this.fetchLedger();
            Swal.fire('Berhasil', 'Entry berhasil diupdate', 'success');
        } catch (error) {
            console.error('Error updating ledger entry:', error);
            Swal.fire('Error', 'Gagal mengupdate entry', 'error');
        }
    }
    
    cancelEdit(id) {
        const row = document.querySelector(`tr[data-id="${id}"]`);
        if (!row) return;
        
        // Show view mode, hide edit mode
        row.querySelectorAll('.view-mode').forEach(el => el.style.display = 'block');
        row.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'none');
        
        // Show edit button, hide save/cancel buttons
        row.querySelector('.edit-btn').style.display = 'inline-block';
        row.querySelector('.save-btn').style.display = 'none';
        row.querySelector('.cancel-btn').style.display = 'none';
        row.querySelector('.delete-btn').style.display = 'inline-block';
        
        // Reset input values to original
        this.fetchLedger();
    }

    async deleteLedgerEntry(id) {
        try {
            const result = await Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Entry ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            });

            if (!result.isConfirmed) return;

            const response = await fetch(`/api/ledgers/${id}?account_id=${this.currentAccountId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error('Failed to delete ledger entry');
            }

            await this.fetchLedger();
            Swal.fire('Terhapus!', 'Entry berhasil dihapus', 'success');
        } catch (error) {
            console.error('Error deleting ledger entry:', error);
            Swal.fire('Error', 'Gagal menghapus entry', 'error');
        }
    }

    formatNumber(num) {
        return parseFloat(num || 0).toLocaleString('id-ID', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    formatCurrencyInput(input) {
        let value = input.value.replace(/[^\d]/g, '');
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
        }
        input.value = value;
    }

    parseCurrency(value) {
        if (!value) return 0;
        return parseInt(value.replace(/[^\d]/g, '')) || 0;
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new LedgerApp();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\akuntansi\resources\views/ledgers/index.blade.php ENDPATH**/ ?>