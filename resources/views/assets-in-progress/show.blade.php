@extends('layouts.app')

@section('title', 'Detail Aset Dalam Penyelesaian')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Detail Aset Dalam Penyelesaian</h3>
                    <div>
                        <a href="{{ route('assets-in-progress.index') }}" class="btn btn-secondary">
                            Kembali ke Daftar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Kode Aset</strong></td>
                                    <td>: {{ $asset->code }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Aset</strong></td>
                                    <td>: {{ $asset->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kelompok</strong></td>
                                    <td>: {{ $asset->group }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Kondisi</strong></td>
                                    <td>: {{ $asset->condition }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>: 
                                        <span class="badge badge-{{ $asset->is_active ? 'success' : 'secondary' }}">
                                            {{ $asset->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Lokasi</strong></td>
                                    <td>: {{ $asset->location ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Tanggal Perolehan</strong></td>
                                    <td>: {{ $asset->acquisition_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Harga Perolehan</strong></td>
                                    <td>: Rp {{ number_format($asset->acquisition_price, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Akun Aset</strong></td>
                                    <td>: {{ $asset->assetAccount->kode ?? '-' }} - {{ $asset->assetAccount->nama ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Oleh</strong></td>
                                    <td>: {{ $asset->creator->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Dibuat</strong></td>
                                    <td>: {{ $asset->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($asset->journals->count() > 0)
                    <div class="mt-4">
                        <h5>Jurnal Terkait</h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Nomor Jurnal</th>
                                        <th>Deskripsi</th>
                                        <th>Debit</th>
                                        <th>Kredit</th>
                                        <th>Sumber</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($asset->journals as $journal)
                                    <tr>
                                        <td>{{ $journal->date->format('d/m/Y') }}</td>
                                        <td>{{ $journal->number }}</td>
                                        <td>{{ $journal->description }}</td>
                                        <td>{{ number_format($journal->total_debit, 0, ',', '.') }}</td>
                                        <td>{{ number_format($journal->total_credit, 0, ',', '.') }}</td>
                                        <td>{{ ucfirst($journal->source_module) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <div class="mt-4">
                        <div class="alert alert-info">
                            <strong>Catatan:</strong> Aset ini masih dalam tahap penyelesaian dan belum dapat disusutkan. 
                            Untuk mengubahnya menjadi aset tetap yang dapat disusutkan, gunakan fitur "Reklasifikasi" 
                            dari halaman daftar Aset Dalam Penyelesaian.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection