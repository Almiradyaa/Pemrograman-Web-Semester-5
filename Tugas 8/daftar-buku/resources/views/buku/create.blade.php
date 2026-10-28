@extends('layouts.app')

@section('judul', 'Tambah Buku')

@section('konten')
    <div class="bg-white rounded-xl shadow-sm border border-stone-200 p-6">
        <div class="border-b border-pale-oak pb-3 mb-6">
            <h1 class="text-2xl font-bold text-espresso">Tambah Buku Baru</h1>
            <p class="text-xs text-stone-500 italic">Isi data buku sesuai koleksi terbaru</p>
        </div>

        <form action="{{ route('buku.store') }}" method="POST">
            @csrf
            @include('buku._form')

            <div class="mt-8 flex gap-3">
                <button class="btn-espresso px-5 py-2 rounded-lg text-sm">Simpan Buku</button>
                <a href="{{ route('buku.index') }}" class="px-5 py-2 rounded-lg border border-stone-300 text-stone-700 hover:bg-stone-100 text-sm">Batal</a>
            </div>
        </form>
    </div>
@endsection