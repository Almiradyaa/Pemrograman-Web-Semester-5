<div class="space-y-5">
    <div>
        <label for="judul" class="block font-semibold mb-1 text-espresso">Judul Buku</label>
        <input type="text" id="judul" name="judul" 
            value="{{ old('judul', $buku->judul ?? '') }}" 
            placeholder="Contoh: Bumi Manusia"
            class="w-full rounded-lg border px-3 py-2 focus-espresso bg-stone-50 @error('judul') border-red-500 @else border-stone-300 @enderror">
        @error('judul')
            <p class="text-sm text-red-600 mt-1 font-sans">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="penulis" class="block font-semibold mb-1 text-espresso">Penulis</label>
        <input type="text" id="penulis" name="penulis" 
            value="{{ old('penulis', $buku->penulis ?? '') }}" 
            placeholder="Contoh: Pramoedya Ananta Toer"
            class="w-full rounded-lg border px-3 py-2 focus-espresso bg-stone-50 @error('penulis') border-red-500 @else border-stone-300 @enderror">
        @error('penulis')
            <p class="text-sm text-red-600 mt-1 font-sans">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tahun_terbit" class="block font-semibold mb-1 text-espresso">Tahun Terbit</label>
        <input type="number" id="tahun_terbit" name="tahun_terbit" 
            value="{{ old('tahun_terbit', $buku->tahun_terbit ?? '') }}" 
            placeholder="1980"
            class="w-40 rounded-lg border px-3 py-2 focus-espresso bg-stone-50 @error('tahun_terbit') border-red-500 @else border-stone-300 @enderror">
        @error('tahun_terbit')
            <p class="text-sm text-red-600 mt-1 font-sans">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="sinopsis" class="block font-semibold mb-1 text-espresso">Sinopsis <span class="text-stone-500 italic text-sm">(opsional)</span></label>
        <textarea id="sinopsis" name="sinopsis" rows="4" 
                placeholder="Tuliskan sinopsis atau ringkasan buku..."
                class="w-full rounded-lg border border-stone-300 bg-stone-50 px-3 py-2 focus-espresso italic">{{ old('sinopsis', $buku->sinopsis ?? '') }}</textarea>
    </div>
</div>