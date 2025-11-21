@extends('layouts.app')

@section('title', 'Master Ledger')

@section('page-header')
<h2 class="page-title">Master Ledger</h2>
@endsection

@section('content')
<!-- Alert Messages -->
<div id="alert-container"></div>

<!-- Create Form -->
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Tambah Ledger Baru</h3>
    </div>
    <div class="card-body">
        <form id="create-form">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Kode</label>
                        <input type="text" name="kode" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tipe Akun</label>
                        <select name="tipe_akun" class="form-select" required>
                            <option value="">Pilih Tipe Akun</option>
                            <option value="aset">Aset</option>
                            <option value="kewajiban">Kewajiban</option>
                            <option value="ekuitas">Ekuitas</option>
                            <option value="pendapatan">Pendapatan</option>
                            <option value="beban">Beban</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Grup</label>
                        <input type="text" name="grup" class="form-control" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Saldo Normal</label>
                        <select name="saldo_normal" class="form-select" required>
                            <option value="">Pilih Saldo Normal</option>
                            <option value="debit">Debit</option>
                            <option value="kredit">Kredit</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Simpan Ledger</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Ledger</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-vcenter card-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Tipe Akun</th>
                    <th>Grup</th>
                    <th>Saldo Normal</th>
                    <th class="w-1">Aksi</th>
                </tr>
            </thead>
            <tbody id="ledger-table">
                @foreach($ledgers as $ledger)
                <tr data-id="{{ $ledger->id }}">
                    <td class="editable" data-field="kode">{{ $ledger->kode }}</td>
                    <td class="editable" data-field="nama">{{ $ledger->nama }}</td>
                    <td class="editable" data-field="tipe_akun">{{ ucfirst($ledger->tipe_akun) }}</td>
                    <td class="editable" data-field="grup">{{ $ledger->grup }}</td>
                    <td class="editable" data-field="saldo_normal">{{ ucfirst($ledger->saldo_normal) }}</td>
                    <td class="actions">
                        <div class="d-grid gap-1">
                            <button class="btn btn-sm btn-warning edit-btn">Edit</button>
                            <button class="btn btn-sm btn-success save-btn" style="display: none;">Simpan</button>
                            <button class="btn btn-sm btn-secondary cancel-btn" style="display: none;">Batal</button>
                            <button class="btn btn-sm btn-danger delete-btn">Hapus</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal modal-blur fade" id="delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">Konfirmasi Hapus</div>
                <div>Apakah Anda yakin ingin menghapus ledger ini?</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancel-delete" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>

    <script>
        class LedgerManager {
            constructor() {
                this.currentEditRow = null;
                this.deleteId = null;
                this.init();
            }

            init() {
                this.bindEvents();
            }

            bindEvents() {
                // Create form
                document.getElementById('create-form').addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.createLedger();
                });

                // Table events
                document.getElementById('ledger-table').addEventListener('click', (e) => {
                    if (e.target.classList.contains('edit-btn')) {
                        this.editRow(e.target.closest('tr'));
                    } else if (e.target.classList.contains('save-btn')) {
                        this.saveRow(e.target.closest('tr'));
                    } else if (e.target.classList.contains('cancel-btn')) {
                        this.cancelEdit(e.target.closest('tr'));
                    } else if (e.target.classList.contains('delete-btn')) {
                        this.showDeleteModal(e.target.closest('tr').dataset.id);
                    }
                });

                // Modal events
                document.getElementById('confirm-delete').addEventListener('click', () => {
                    this.deleteLedger();
                });

                document.getElementById('cancel-delete').addEventListener('click', () => {
                    this.hideDeleteModal();
                });


            }

            async createLedger() {
                const form = document.getElementById('create-form');
                const formData = new FormData(form);
                const data = Object.fromEntries(formData);

                try {
                    const response = await fetch('/ledger', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showAlert('success', result.message);
                        this.addRowToTable(result.data);
                        form.reset();
                    } else {
                        this.showAlert('error', result.message);
                    }
                } catch (error) {
                    this.showAlert('error', 'Terjadi kesalahan saat menyimpan data');
                }
            }

            editRow(row) {
                if (this.currentEditRow) {
                    this.cancelEdit(this.currentEditRow);
                }

                this.currentEditRow = row;
                row.classList.add('editing');

                const cells = row.querySelectorAll('.editable');
                cells.forEach(cell => {
                    const field = cell.dataset.field;
                    const value = cell.textContent.trim();
                    
                    if (field === 'tipe_akun') {
                        cell.innerHTML = `
                            <select class="form-select form-select-sm">
                                <option value="aset" ${value.toLowerCase() === 'aset' ? 'selected' : ''}>Aset</option>
                                <option value="kewajiban" ${value.toLowerCase() === 'kewajiban' ? 'selected' : ''}>Kewajiban</option>
                                <option value="ekuitas" ${value.toLowerCase() === 'ekuitas' ? 'selected' : ''}>Ekuitas</option>
                                <option value="pendapatan" ${value.toLowerCase() === 'pendapatan' ? 'selected' : ''}>Pendapatan</option>
                                <option value="beban" ${value.toLowerCase() === 'beban' ? 'selected' : ''}>Beban</option>
                            </select>
                        `;
                    } else if (field === 'saldo_normal') {
                        cell.innerHTML = `
                            <select class="form-select form-select-sm">
                                <option value="debit" ${value.toLowerCase() === 'debit' ? 'selected' : ''}>Debit</option>
                                <option value="kredit" ${value.toLowerCase() === 'kredit' ? 'selected' : ''}>Kredit</option>
                            </select>
                        `;
                    } else {
                        cell.innerHTML = `<input type="text" class="form-control form-control-sm" value="${value}">`;
                    }
                });

                // Toggle buttons
                row.querySelector('.edit-btn').style.display = 'none';
                row.querySelector('.save-btn').style.display = 'inline-block';
                row.querySelector('.cancel-btn').style.display = 'inline-block';
                row.querySelector('.delete-btn').style.display = 'none';
            }

            async saveRow(row) {
                const id = row.dataset.id;
                const data = {};

                const cells = row.querySelectorAll('.editable');
                cells.forEach(cell => {
                    const field = cell.dataset.field;
                    const input = cell.querySelector('.form-control, .form-select');
                    data[field] = input.value;
                });

                try {
                    const response = await fetch(`/ledger/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(data)
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showAlert('success', result.message);
                        this.updateRowDisplay(row, result.data);
                        this.exitEditMode(row);
                    } else {
                        this.showAlert('error', result.message);
                    }
                } catch (error) {
                    this.showAlert('error', 'Terjadi kesalahan saat mengupdate data');
                }
            }

            cancelEdit(row) {
                location.reload();
            }

            exitEditMode(row) {
                row.classList.remove('editing');
                row.querySelector('.edit-btn').style.display = 'inline-block';
                row.querySelector('.save-btn').style.display = 'none';
                row.querySelector('.cancel-btn').style.display = 'none';
                row.querySelector('.delete-btn').style.display = 'inline-block';
                this.currentEditRow = null;
            }

            updateRowDisplay(row, data) {
                const cells = row.querySelectorAll('.editable');
                cells.forEach(cell => {
                    const field = cell.dataset.field;
                    let value = data[field];
                    
                    if (field === 'tipe_akun' || field === 'saldo_normal') {
                        value = value.charAt(0).toUpperCase() + value.slice(1);
                    }
                    
                    cell.textContent = value;
                });
            }

            showDeleteModal(id) {
                this.deleteId = id;
                const modal = new bootstrap.Modal(document.getElementById('delete-modal'));
                modal.show();
            }

            hideDeleteModal() {
                this.deleteId = null;
                const modal = bootstrap.Modal.getInstance(document.getElementById('delete-modal'));
                if (modal) modal.hide();
            }

            async deleteLedger() {
                try {
                    const response = await fetch(`/ledger/${this.deleteId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showAlert('success', result.message);
                        document.querySelector(`tr[data-id="${this.deleteId}"]`).remove();
                    } else {
                        this.showAlert('error', result.message);
                    }
                } catch (error) {
                    this.showAlert('error', 'Terjadi kesalahan saat menghapus data');
                }

                this.hideDeleteModal();
            }

            addRowToTable(data) {
                const tbody = document.getElementById('ledger-table');
                const row = document.createElement('tr');
                row.dataset.id = data.id;
                row.innerHTML = `
                    <td class="editable" data-field="kode">${data.kode}</td>
                    <td class="editable" data-field="nama">${data.nama}</td>
                    <td class="editable" data-field="tipe_akun">${data.tipe_akun.charAt(0).toUpperCase() + data.tipe_akun.slice(1)}</td>
                    <td class="editable" data-field="grup">${data.grup}</td>
                    <td class="editable" data-field="saldo_normal">${data.saldo_normal.charAt(0).toUpperCase() + data.saldo_normal.slice(1)}</td>
                    <td class="actions">
                        <div class="d-grid gap-1">
                            <button class="btn btn-sm btn-warning edit-btn">Edit</button>
                            <button class="btn btn-sm btn-success save-btn" style="display: none;">Simpan</button>
                            <button class="btn btn-sm btn-secondary cancel-btn" style="display: none;">Batal</button>
                            <button class="btn btn-sm btn-danger delete-btn">Hapus</button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            }

            showAlert(type, message) {
                const container = document.getElementById('alert-container');
                const alert = document.createElement('div');
                alert.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible`;
                alert.innerHTML = `
                    <div class="d-flex">
                        <div>${message}</div>
                    </div>
                    <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                `;
                
                container.innerHTML = '';
                container.appendChild(alert);
                
                setTimeout(() => {
                    alert.remove();
                }, 5000);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            new LedgerManager();
        });
    </script>
@endsection