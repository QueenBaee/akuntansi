@extends('layouts.app')

@section('title', 'Trial Balance')

@section('page-header')
    <div class="page-pretitle">Master Data</div>
    <h2 class="page-title">Trial Balance</h2>
@endsection

@section('page-actions')
    <a href="{{ route('trial-balance.create') }}" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <line x1="12" y1="5" x2="12" y2="19"/>
            <line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tambah Root
    </a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Trial Balance</h3>

                <div class="card-actions">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search"
                            value="{{ request('search') }}"
                            class="form-control me-2"
                            placeholder="Search Kode / Keterangan">
                        <button class="btn btn-outline-primary">Search</button>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-vcenter card-table">
                    <thead>
                        <tr>
                            <th>Kode TB</th>
                            <th>Akun TB</th>
                            <th>Parent</th>
                            <th>Level</th>
                            <th>Kas/Bank</th>
                            <th>2024 (Rp)</th>
                            <th class="w-1">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            function renderRowsTB($items, $prefix = '') {
                                foreach ($items as $item) {
                                    if(substr($item->kode, 0, 1) == 'E') continue;

                                    echo '<tr>';
                                    echo '<td>' . $item->kode . '</td>';
                                    echo '<td>' . $prefix . $item->keterangan . '</td>';
                                    echo '<td>' . ($item->parent?->kode ?? '-') . '</td>';
                                    echo '<td>' . $item->level . '</td>';

                                    // Kas/Bank hanya untuk level 3
                                    echo '<td>';
                                    if ($item->level == 3) {
                                        echo $item->is_kas_bank ? ucfirst($item->is_kas_bank) : '-';
                                    } else {
                                        echo '-';
                                    }
                                    echo '</td>';

                                    echo '<td>' . number_format($item->tahun_2024 ?? 0, 0, ',', '.') . '</td>';

                                    echo '<td>
                                            <div class="btn-list flex-nowrap">
                                                <a href="' . route('trial-balance.edit', $item->id) . '" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <a href="' . route('trial-balance.create') . '?parent_id=' . $item->id . '" class="btn btn-sm btn-outline-success">Tambah</a>
                                                <form action="' . route('trial-balance.destroy', $item->id) . '" method="POST" class="d-inline">
                                                    ' . csrf_field() . '
                                                    ' . method_field('DELETE') . '
                                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Hapus?\')">Hapus</button>
                                                </form>
                                            </div>
                                          </td>';
                                    echo '</tr>';

                                    if ($item->children->count() > 0) {
                                        renderRowsTB($item->children, $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;');
                                    }
                                }
                            }
                            renderRowsTB($items);
                        @endphp
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Bagian Beban --}}
        <div class="card mt-4">
            <div class="card-header">
                <h3 class="card-title">Beban (E) Group</h3>
            </div>

            <div class="card-body p-0">
                @foreach($bebanItems as $group => $groupItems)
                    <h5 class="m-3">{{ $group }}</h5>

                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Keterangan</th>
                                    <th>Parent</th>
                                    <th>Level</th>
                                    <th>Kas/Bank</th>
                                    <th>2024 (Rp)</th>
                                    <th class="w-1">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupItems as $item)
                                <tr>
                                    <td>{{ $item->kode }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->parent?->kode ?? '-' }}</td>
                                    <td>{{ $item->level }}</td>

                                    {{-- Kas/Bank di beban --}}
                                    <td>
                                        @if ($item->level == 3)
                                            {{ $item->is_kas_bank ? ucfirst($item->is_kas_bank) : '-' }}
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <td>{{ number_format($item->tahun_2024 ?? 0, 0, ',', '.') }}</td>

                                    <td>
                                        <div class="btn-list flex-nowrap">
                                            <a href="{{ route('trial-balance.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="{{ route('trial-balance.create') }}?parent_id={{ $item->id }}" class="btn btn-sm btn-outline-success">Tambah</a>
                                            <form action="{{ route('trial-balance.destroy', $item->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus?')">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection
