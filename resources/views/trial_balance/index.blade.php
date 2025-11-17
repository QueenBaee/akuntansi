@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Trial Balance</h4>

    {{-- Search Form --}}
    <form method="GET" action="{{ route('trial-balance.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search Kode / Keterangan">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    <a href="{{ route('trial-balance.create') }}" class="btn btn-primary mb-3">Tambah Root</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Keterangan</th>
                <th>Parent</th>
                <th>Level</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @php
                function renderRows($items, $prefix = '') {
                    foreach ($items as $item) {
                        // Skip Beban (E) karena nanti digroup
                        if(substr($item->kode,0,1) == 'E') continue;

                        echo '<tr>';
                        echo '<td>' . $item->kode . '</td>';
                        echo '<td>' . $prefix . $item->keterangan . '</td>';
                        echo '<td>' . ($item->parent?->kode ?? '-') . '</td>';
                        echo '<td>' . $item->level . '</td>';
                        echo '<td>
                                <a href="' . route('trial-balance.edit', $item->id) . '" class="btn btn-warning btn-sm">Edit</a>
                                <a href="' . route('trial-balance.create') . '?parent_id=' . $item->id . '" class="btn btn-success btn-sm">Tambah Sub-Akun</a>
                                <form action="' . route('trial-balance.destroy', $item->id) . '" method="POST" style="display:inline-block">
                                    ' . csrf_field() . '
                                    ' . method_field('DELETE') . '
                                    <button onclick="return confirm(\'Hapus?\')" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                              </td>';
                        echo '</tr>';
                        if ($item->children->count() > 0) {
                            renderRows($item->children, $prefix . '    ');
                        }
                    }
                }
                renderRows($items);
            @endphp
        </tbody>
    </table>

    {{-- Beban (E) Group --}}
    <h5 class="mt-5">Beban (E) Group</h5>
    @foreach($bebanItems as $group => $groupItems)
        <h6>{{ $group }}</h6>
        <table class="table table-bordered table-sm mb-3">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Keterangan</th>
                    <th>Parent</th>
                    <th>Level</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupItems as $item)
                    <tr>
                        <td>{{ $item->kode }}</td>
                        <td>{{ $item->keterangan }}</td>
                        <td>{{ $item->parent?->kode ?? '-' }}</td>
                        <td>{{ $item->level }}</td>
                        <td>
                            <a href="{{ route('trial-balance.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>
                            <a href="{{ route('trial-balance.create') }}?parent_id={{ $item->id }}" class="btn btn-success btn-sm">Tambah Sub-Akun</a>
                            <form action="{{ route('trial-balance.destroy', $item->id) }}" method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Hapus?')" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>
@endsection
