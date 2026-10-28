<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('judul', 'Daftar Buku') - Perpustakaan Digital</title>
    
<!-- Google Fonts: Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="[suspicious link removed]" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --espresso: #3C2415;
            --espresso-dark: #2A190E;
            --pale-oak: #DDD9CE;
            --pale-oak-light: #F4F2ED;
        }

        body {
            font-family: 'Playfair Display', Georgia, serif;
            background-color: var(--pale-oak-light);
            color: var(--espresso);
        }

        .bg-espresso { background-color: var(--espresso); }
        .bg-pale-oak { background-color: var(--pale-oak); }
        .text-espresso { color: var(--espresso); }
        .text-pale-oak { color: var(--pale-oak); }
        .border-espresso { border-color: var(--espresso); }
        .border-pale-oak { border-color: var(--pale-oak); }

        .btn-espresso {
            background-color: var(--espresso);
            color: var(--pale-oak);
            font-weight: 600;
            border: 1px solid var(--espresso);
            transition: all 0.2s ease-in-out;
        }

        .btn-espresso:hover {
            background-color: var(--espresso-dark);
            color: #ffffff;
        }

        .btn-pale-oak {
            background-color: var(--pale-oak);
            color: var(--espresso);
            font-weight: 600;
            transition: all 0.2s ease-in-out;
        }

        .btn-pale-oak:hover {
            background-color: #CFCABF;
        }

        .focus-espresso:focus {
            outline: none;
            border-color: var(--espresso);
            box-shadow: 0 0 0 3px rgba(60, 36, 21, 0.15);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    
<nav class="bg-espresso text-pale-oak shadow-md border-b-2 border-pale-oak">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
        <a href="{{ route('buku.index') }}" class="text-xl font-bold tracking-wide flex items-center gap-2 text-pale-oak">
            <span>📖</span>
            <span>Perpustakaan Mini</span>
        </a>
        <a href="{{ route('buku.create') }}" 
        class="btn-pale-oak px-4 py-2 rounded-lg text-sm transition shadow-sm">
            + Tambah Buku
        </a>
    </div>
</nav>

    <main class="max-w-4xl mx-auto px-4 py-8 grow w-full">
        {{-- Pesan sukses (flash message) --}}
        @if (session('sukses'))
            <div class="mb-6 rounded-lg bg-emerald-50 border-l-4 border-emerald-700 text-emerald-900 px-4 py-3 shadow-sm flex justify-between items-center">
                <div>
                    <span class="font-bold">Berhasil!</span> {{ session('sukses') }}
                </div>
            </div>
        @endif

        {{-- Tempat konten dari tiap view --}}
        @yield('konten')
    </main>

    <footer class="text-center text-xs text-stone-600 py-6 border-t border-stone-300 mt-auto">
        Dibuat dengan Laravel, Blade &amp; Tailwind CSS &middot; Palet Espresso (#3C2415) &amp; Pale Oak (#DDD9CE)
    </footer>
</body>
</html>