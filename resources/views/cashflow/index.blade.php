@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Cashflow</h4>

    {{-- Search Form --}}
    <form method="GET" action="{{ route('cashflow.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" value="{{ request('search') }}" 
                class="form-control" placeholder="Search Kode / Keterangan">
            <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
    </form>

    <a href="{{ route('cashflow.create') }}" class="btn btn-primary mb-3">Tambah Data</a>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Keterangan</th>
                <th>Kategori</th>
                <th>Tipe</th>
                <th>Nominal</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td>{{ $row->kode }}</td>
                    <td>{{ $row->keterangan }}</td>
                    <td>{{ ucfirst($row->kategori) }}</td>
                    <td>{{ ucfirst($row->tipe) }}</td>
                    <td>Rp {{ number_format($row->nominal, 0, ',', '.') }}</td>

                    <td>
                        <a href="{{ route('cashflow.edit', $row->id) }}" 
                        class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('cashflow.destroy', $row->id) }}" 
                            method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Hapus data ini?')" 
                                    class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
