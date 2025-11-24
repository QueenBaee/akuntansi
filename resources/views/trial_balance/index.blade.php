@extends('layouts.app')

@section('content')
<div class="container">

    <h4>Trial Balance</h4>

    <form method="GET" class="mb-3">
        <input type="text" name="search" value="{{ request('search') }}" 
               class="form-control" placeholder="Cari kode / keterangan">
    </form>

    <a href="{{ route('trial-balance.create') }}" class="btn btn-primary mb-3">Tambah Data</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Kode</th>
                <th>Keterangan</th>
                <th>Level</th>
                <th>Parent</th>
                <th>Tahun 2024</th>
                <th>Kas / Bank</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
            <tr>
                <td>{{ $item->kode }}</td>
                <td>{{ $item->keterangan }}</td>
                <td>{{ $item->level }}</td>
                <td>{{ $item->parent?->kode }}</td>
                <td>{{ number_format($item->tahun_2024, 0, ',', '.') }}</td>
                <td>
                    @if($item->is_kas_bank == 'kas')
                        Kas
                    @elseif($item->is_kas_bank == 'bank')
                        Bank
                    @else
                        -
                    @endif
                </td>
                <td>
                    <a href="{{ route('trial-balance.edit', $item->id) }}" class="btn btn-warning btn-sm">Edit</a>

                    <form action="{{ route('trial-balance.destroy', $item->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Hapus data?')">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
