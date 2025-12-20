@extends('layouts.app')

@section('title', 'Aset Dalam Penyelesaian')

@php
use App\Helpers\AssetGroupHelper;
@endphp

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Aset Dalam Penyelesaian</h2>
@endsection

@section('page-actions')
    <div class="btn-list">
        <button type="button" class="btn btn-primary" onclick="showReclassifyModal()" id="reclassifyBtn" disabled>
            Reklasifikasi Terpilih
        </button>
    </div>
@endsection

@push('styles')
<style>
.table-vcenter {
    width: 100%;
}

.table-vcenter td {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.table-vcenter td:nth-child(3) {
    white-space: normal;
    word-wrap: break-word;
}

.group-header {
    background-color: #f8f9fa;
    font-weight: bold;
}

.account-header {
    background-color: #e9ecef;
    font-weight: bold;
}
</style>
@endpush

@section('content')
    <div class="card">
        <div class="table-responsive">
            <table class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th style="text-align:center">
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th style="text-align:center">No</th>
                        <th style="text-align:center">Jenis Aset</th>
                        <th style="text-align:center">Jumlah</th>
                        <th style="text-align:center">Tanggal Perolehan</th>
                        <th style="text-align:center">Tarif (%)</th>
                        <th style="text-align:center">Harga Perolehan</th>
                        <th class="w-1" style="text-align:center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @forelse($groupedAssets ?? [] as $accountName => $accountGroups)
                        <tr class="account-header">
                            <td class="text-center">-</td>
                            <td class="text-center">-</td>
                            <td><strong>{{ $accountName }}</strong></td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        @foreach($accountGroups as $groupName => $assets)
                            <tr class="group-header">
                                <td class="text-center">-</td>
                                <td class="text-center">-</td>
                                <td style="padding-left: 20px;"><strong>{{ AssetGroupHelper::translateGroup($groupName) }}</strong></td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                                <td>-</td>
                            </tr>
                            @foreach($assets as $asset)
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="asset-checkbox" value="{{ $asset->id }}">
                                    </td>
                                    <td class="text-center">{{ $counter++ }}</td>
                                    <td style="padding-left: 40px;">{{ $asset->name }}</td>
                                    <td class="text-center">{{ $asset->quantity ?? 1 }}</td>
                                    <td class="text-center">{{ $asset->acquisition_date ? \Carbon\Carbon::parse($asset->acquisition_date)->format('d/m/Y') : '-' }}</td>
                                    <td class="text-center">{{ $asset->depreciation_rate ? number_format($asset->depreciation_rate, 2) : '-' }}</td>
                                    <td class="text-end">{{ $asset->acquisition_price ? number_format($asset->acquisition_price, 0, ',', '.') : '-' }}</td>
                                    <td>
                                        <a href="/assets-in-progress/{{ $asset->id }}" class="btn btn-sm btn-white">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td class="text-center text-muted">-</td>
                            <td class="text-center text-muted">-</td>
                            <td class="text-center text-muted">Tidak ada aset dalam penyelesaian</td>
                            <td class="text-center text-muted">-</td>
                            <td class="text-center text-muted">-</td>
                            <td class="text-center text-muted">-</td>
                            <td class="text-center text-muted">-</td>
                            <td class="text-center text-muted">-</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
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
    window.location.href = `/assets-in-progress/reclassify?assets=${assetIds}`;
}
</script>
@endpush