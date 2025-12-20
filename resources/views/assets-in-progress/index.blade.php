@extends('layouts.app')

@section('title', 'Aset Dalam Penyelesaian')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Aset Dalam Penyelesaian</h3>
                    <div>
                        <button type="button" class="btn btn-primary" onclick="showReclassifyModal()" id="reclassifyBtn" disabled>
                            Reklasifikasi Terpilih
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($assetsInProgress->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="selectAll">
                                        </th>
                                        <th>Kode</th>
                                        <th>Nama Aset</th>
                                        <th>Tanggal Perolehan</th>
                                        <th>Harga Perolehan</th>
                                        <th>Akun Aset</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assetsInProgress as $asset)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="asset-checkbox" value="{{ $asset->id }}">
                                        </td>
                                        <td>{{ $asset->code }}</td>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->acquisition_date->format('d/m/Y') }}</td>
                                        <td>{{ number_format($asset->acquisition_price, 0, ',', '.') }}</td>
                                        <td>{{ $asset->assetAccount->kode ?? '-' }} - {{ $asset->assetAccount->nama ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $asset->is_active ? 'success' : 'secondary' }}">
                                                {{ $asset->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('assets-in-progress.show', $asset) }}" class="btn btn-sm btn-info">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        {{ $assetsInProgress->links() }}
                    @else
                        <div class="text-center py-4">
                            <p class="text-muted">Tidak ada aset dalam penyelesaian</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const assetCheckboxes = document.querySelectorAll('.asset-checkbox');
    const reclassifyBtn = document.getElementById('reclassifyBtn');

    selectAllCheckbox.addEventListener('change', function() {
        assetCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateReclassifyButton();
    });

    assetCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateReclassifyButton();
        });
    });

    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
        selectAllCheckbox.checked = checkedBoxes.length === assetCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < assetCheckboxes.length;
    }

    function updateReclassifyButton() {
        const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
        reclassifyBtn.disabled = checkedBoxes.length === 0;
    }
});

function showReclassifyModal() {
    const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Pilih minimal satu aset untuk direklasifikasi');
        return;
    }

    const assetIds = Array.from(checkedBoxes).map(cb => cb.value).join(',');
    window.location.href = `{{ route('assets-in-progress.reclassify') }}?assets=${assetIds}`;
}
</script>
@endsection