@extends('layouts.app')

@section('judul', 'Ubah Buku')

@section('konten')
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <div class="border-b border-pale-oak pb-3 mb-6">
            <h1 class="text-2xl font-bold text-espresso">Ubah Data Buku</h1>
            <p class="text-xs text-stone-500 italic">Perbarui rincian informasi buku</p>
        </div>

        <form action="{{ route('buku.update', $buku) }}" method="POST">
            @csrf
            @method('PUT')
            @include('buku._form')

            <div class="mt-8 flex gap-3">
                <button class="btn-espresso px-5 py-2 rounded-lg text-sm">Simpan Perubahan</button>
                <a href="{{ route('buku.index') }}" class="px-5 py-2 rounded-lg border border-stone-300 text-stone-700 hover:bg-stone-100 text-sm">Batal</a>
            </div>
        </form>
    </div>
@endsection