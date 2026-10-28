@extends('layouts.app')

@section('judul', 'Daftar Buku')

@section('konten')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 border-b-2 border-pale-oak pb-4">
        <div>
            <h1 class="text-2xl font-bold text-espresso">Daftar Koleksi Buku</h1>
            <p class="text-xs text-stone-600 italic">Katalog dan arsip buku digital</p>
        </div>
        
        {{-- Form Pencarian --}}
        <form action="{{ route('buku.index') }}" method="GET" class="flex gap-2">
            <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari judul / penulis..." 
                class="w-60 rounded-lg border border-stone-300 bg-white px-3 py-2 text-sm focus-espresso">
            <button class="btn-espresso px-4 py-2 rounded-lg text-sm">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-stone-200 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-espresso text-pale-oak text-xs uppercase font-bold tracking-wider">
                <tr>
                    <th class="px-4 py-3">Judul</th>
                    <th class="px-4 py-3">Penulis</th>
                    <th class="px-4 py-3">Tahun</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 text-sm">
                @forelse ($daftarBuku as $buku)
                    <tr class="hover:bg-stone-50 transition">
                        <td class="px-4 py-3 font-semibold">
                            <a href="{{ route('buku.show', $buku) }}" class="text-espresso hover:underline">
                                {{ $buku->judul }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-stone-700">{{ $buku->penulis }}</td>
                        <td class="px-4 py-3 text-stone-600">{{ $buku->tahun_terbit }}</td>
                        <td class="px-4 py-3">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('buku.edit', $buku) }}" 
                                class="px-3 py-1 rounded border border-stone-300 bg-stone-100 text-stone-800 hover:bg-stone-200 font-medium text-xs">Ubah</a>
                                
                                <form action="{{ route('buku.destroy', $buku) }}" method="POST" 
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="px-3 py-1 rounded border border-red-200 bg-red-50 text-red-800 hover:bg-red-100 font-medium text-xs">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-stone-500 italic">
                            Belum ada data buku dalam perpustakaan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Link Navigasi Pagination --}}
    <div class="mt-6">
        {{ $daftarBuku->links() }}
    </div>
@endsection