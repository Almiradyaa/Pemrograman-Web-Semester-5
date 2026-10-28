@extends('layouts.app')

@section('judul', $buku->judul)

@section('konten')
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <div class="border-b-2 border-pale-oak pb-4">
            <h1 class="text-3xl font-bold text-espresso">{{ $buku->judul }}</h1>
            <p class="text-sm text-stone-600 mt-1">
                Penulis: <span class="font-semibold text-espresso">{{ $buku->penulis }}</span> &middot; 
                Tahun Terbit: <span class="font-semibold text-espresso">{{ $buku->tahun_terbit }}</span>
            </p>
        </div>

        <div class="mt-6">
            <h2 class="text-xs font-bold uppercase text-espresso tracking-wider mb-2">Sinopsis Buku</h2>
            <div class="text-stone-800 italic leading-relaxed bg-stone-50 p-5 rounded-lg border border-stone-200 text-base">
                {{ $buku->sinopsis ?: 'Belum ada sinopsis untuk buku ini.' }}
            </div>
        </div>

        <div class="mt-8 flex gap-3 border-t border-stone-200 pt-4">
            <a href="{{ route('buku.edit', $buku) }}" class="btn-pale-oak px-4 py-2 rounded-lg text-sm">Ubah Buku</a>
            <a href="{{ route('buku.index') }}" class="px-4 py-2 rounded-lg border border-stone-300 text-stone-700 hover:bg-stone-100 text-sm">&larr; Kembali ke Daftar</a>
        </div>
    </div>
@endsection