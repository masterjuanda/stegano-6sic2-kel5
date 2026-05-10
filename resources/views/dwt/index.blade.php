@extends('layouts.app')

@section('title', 'DWT Steganografi')

@section('content')

{{-- Header --}}
<div class="mb-8">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">DWT Steganografi</h1>
            <p class="text-sm text-gray-500">Discrete Wavelet Transform — Haar Wavelet 1-level</p>
        </div>
    </div>
    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 mt-4">
        <div class="flex gap-3">
            <span class="text-indigo-500 text-lg">ℹ️</span>
            <p class="text-sm text-indigo-700 leading-relaxed">
                DWT memecah gambar menjadi koefisien <strong>rata-rata (cA)</strong> dan
                <strong>selisih (cD)</strong>. Pesan disisipkan pada cD dengan aturan:
                <code class="bg-indigo-100 px-1 rounded">bit=1 → ganjil</code>,
                <code class="bg-indigo-100 px-1 rounded">bit=0 → genap</code>.
            </p>
        </div>
    </div>
</div>

{{-- Flash Messages --}}
@if (session('dwt_error'))
    <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">
        <span class="text-lg">❌</span>
        <p class="text-sm font-medium">{{ session('dwt_error') }}</p>
    </div>
@endif

@if (session('dwt_success'))
    <div class="mb-6 flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3">
        <span class="text-lg">✅</span>
        <p class="text-sm font-medium">{{ session('dwt_success') }}</p>
    </div>
@endif

@if (session('dwt_decoded_message'))
    <div class="mb-6 bg-indigo-50 border border-indigo-200 rounded-xl px-4 py-4">
        <div class="flex items-center gap-2 mb-2">
            <span class="text-lg">🔓</span>
            <p class="text-sm font-bold text-indigo-800">Pesan Tersembunyi Berhasil Didekode!</p>
        </div>
        <div class="bg-white border border-indigo-100 rounded-lg px-3 py-2">
            <p class="text-indigo-900 font-mono text-sm break-words">{{ session('dwt_decoded_message') }}</p>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3">
        <p class="text-sm font-bold mb-1">❌ Terdapat kesalahan:</p>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Form Encode --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-8">
    <div class="bg-gradient-to-r from-indigo-600 to-indigo-500 px-6 py-4">
        <h2 class="text-white font-bold text-lg">🔐 Encode Pesan</h2>
        <p class="text-indigo-100 text-xs mt-1">Upload gambar dan sisipkan pesan rahasia menggunakan DWT</p>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ route('dwt.encode') }}" enctype="multipart/form-data"
              class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @csrf

            {{-- Kiri: Judul + Upload + Preview --}}
            <div class="space-y-4">

                {{-- Judul --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Judul / Label
                        <span class="text-gray-400 font-normal">(opsional)</span>
                    </label>
                    <input type="text"
                           name="judul"
                           value="{{ old('judul') }}"
                           maxlength="100"
                           placeholder="Contoh: Foto Kucing, Gambar Tes..."
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" />
                    <p class="text-xs text-gray-400 mt-1">
                        Jika dikosongkan, nama file akan digunakan sebagai judul
                    </p>
                </div>

                {{-- Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Upload Gambar <span class="text-red-500">*</span>
                    </label>
                    <input type="file"
                           name="gambar"
                           id="gambar-input"
                           accept="image/png,image/jpeg,image/jpg"
                           onchange="previewImage(this)"
                           class="block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-lg file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-indigo-50 file:text-indigo-700
                                  hover:file:bg-indigo-100 cursor-pointer
                                  border border-gray-300 rounded-lg p-1" />
                    <p class="text-xs text-gray-400 mt-1">PNG / JPG · Maks 2MB</p>
                </div>

                {{-- Preview --}}
                <div id="preview-container" class="hidden">
                    <p class="text-xs font-medium text-gray-500 mb-2">Preview Gambar:</p>
                    <img id="preview-img"
                         src=""
                         alt="Preview"
                         class="max-h-48 rounded-xl border border-gray-200 shadow-sm object-contain w-full bg-gray-50" />
                </div>
            </div>

            {{-- Kanan: Pesan + Submit --}}
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Pesan Rahasia <span class="text-red-500">*</span>
                    </label>
                    <textarea name="pesan_rahasia"
                              rows="6"
                              maxlength="1000"
                              placeholder="Ketik pesan rahasia yang ingin disembunyikan..."
                              class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm
                                     focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                     resize-none transition">{{ old('pesan_rahasia') }}</textarea>
                    <p class="text-xs text-gray-400 mt-1">Maksimal 1000 karakter</p>
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold
                               py-3 px-6 rounded-xl transition flex items-center justify-center gap-2">
                    🔒 Encode dengan DWT
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Daftar Gambar Tersimpan --}}
@if ($items->count() > 0)
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-800">🖼️ Gambar Tersimpan</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $items->count() }} gambar · Klik Decode untuk membaca pesan</p>
        </div>
    </div>

    <div class="divide-y divide-gray-100">
        @foreach ($items as $item)
        <div class="p-5 flex flex-col sm:flex-row gap-4 items-start sm:items-center
                    {{ session('dwt_decoded_id') == $item->id ? 'bg-indigo-50' : 'hover:bg-gray-50' }}
                    transition">

            {{-- Thumbnail --}}
            <div class="flex-shrink-0">
                <img src="{{ $item->gambar_url }}"
                     alt="{{ $item->judul }}"
                     class="w-20 h-20 object-contain rounded-xl border border-gray-200 shadow-sm bg-gray-50" />
            </div>

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                {{-- Judul --}}
                <p class="text-sm font-semibold text-gray-800 truncate">
                    {{ $item->judul }}
                </p>
                {{-- Nama file asli --}}
                <p class="text-xs text-gray-400 mt-0.5 truncate">
                    📄 {{ $item->nama_file }}
                </p>
                {{-- Tanggal --}}
                <p class="text-xs text-gray-400 mt-0.5">
                    🕐 {{ $item->created_at->format('d M Y, H:i') }}
                </p>
                {{-- Status badge --}}
                <span class="inline-flex items-center gap-1 mt-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium
                    {{ $item->is_encoded ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">
                    {{ $item->is_encoded ? '🔒 Mengandung pesan DWT' : '🖼️ Tanpa pesan' }}
                </span>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap gap-2 flex-shrink-0">
                {{-- Decode --}}
                <form method="POST" action="{{ route('dwt.decode', $item) }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100
                                   border border-emerald-200 text-emerald-700 text-xs font-semibold
                                   px-3 py-2 rounded-lg transition">
                        🔓 Decode
                    </button>
                </form>

                {{-- Download --}}
                <a href="{{ route('dwt.download', $item) }}"
                   class="inline-flex items-center gap-1.5 bg-gray-800 hover:bg-gray-900
                          text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                    ⬇️ Download
                </a>

                {{-- Hapus --}}
                <form method="POST" action="{{ route('dwt.destroy', $item) }}"
                      onsubmit="return confirm('Yakin ingin menghapus gambar ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 bg-red-50 hover:bg-red-100
                                   border border-red-200 text-red-700 text-xs font-semibold
                                   px-3 py-2 rounded-lg transition">
                        🗑️ Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="text-center py-16 text-gray-400">
    <div class="text-5xl mb-3">🌊</div>
    <p class="text-sm">Belum ada gambar DWT. Upload gambar di atas untuk mulai!</p>
</div>
@endif

{{-- Preview Script --}}
<script>
function previewImage(input) {
    const container = document.getElementById('preview-container');
    const img       = document.getElementById('preview-img');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            img.src = e.target.result;
            container.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

@endsection