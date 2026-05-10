<?php

namespace App\Http\Controllers;

use App\Models\Dwt;
use App\Services\DwtService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DwtController extends Controller
{
    public function __construct(
        private readonly DwtService $dwt
    ) {}

    /**
     * GET /dwt — Halaman utama DWT, tampilkan semua gambar milik user.
     */
    public function index(): View
    {
        $items = Dwt::where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('dwt.index', compact('items'));
    }

    /**
     * POST /dwt/encode — Upload gambar + encode pesan DWT.
     */
    public function encode(Request $request): RedirectResponse
    {
        $request->validate([
            'judul'         => ['nullable', 'string', 'max:100'],
            'gambar'        => ['required', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'pesan_rahasia' => ['required', 'string', 'max:1000'],
        ], [
            'gambar.required'        => 'Gambar wajib diupload.',
            'gambar.mimes'           => 'Format gambar harus PNG atau JPG.',
            'gambar.max'             => 'Ukuran gambar maksimal 2MB.',
            'pesan_rahasia.required' => 'Pesan rahasia tidak boleh kosong.',
            'pesan_rahasia.max'      => 'Pesan maksimal 1000 karakter.',
        ]);

        $disk = Storage::disk('public');

        // Simpan gambar asli sementara
        $originalPath     = $request->file('gambar')->store('dwt', 'public');
        $absoluteOriginal = $disk->path($originalPath);
        $namaFile         = $request->file('gambar')->getClientOriginalName();

        // Tentukan path output encoded
        $encodedFilename = 'dwt_encoded_' . pathinfo(basename($originalPath), PATHINFO_FILENAME) . '.png';
        $encodedRelPath  = 'dwt/' . $encodedFilename;
        $absoluteOutput  = $disk->path($encodedRelPath);

        // Jalankan DWT encode
        $result = $this->dwt->encode(
            inputPath: $absoluteOriginal,
            outputPath: $absoluteOutput,
            message: $request->input('pesan_rahasia'),
        );

        // Hapus file asli setelah encode
        $disk->delete($originalPath);

        if (! $result['success']) {
            Log::error('DwtController::encode failed', ['error' => $result['error']]);
            return back()->with('dwt_error', 'Encode DWT gagal: ' . $result['error']);
        }

        // Simpan ke database
        Dwt::create([
            'user_id'    => auth()->id(),
            'judul'      => $request->input('judul') ?: $namaFile,
            'nama_file'  => $namaFile,
            'gambar'     => $encodedRelPath,
            'is_encoded' => true,
        ]);

        return back()->with('dwt_success', '✅ Pesan berhasil di-encode menggunakan DWT Haar!');
    }

    /**
     * POST /dwt/{dwt}/decode — Decode pesan dari gambar yang sudah tersimpan.
     */
    public function decode(Request $request, Dwt $dwt): RedirectResponse
    {
        $disk      = Storage::disk('public');
        $imagePath = $disk->path($dwt->gambar);

        if (! $disk->exists($dwt->gambar)) {
            return back()->with('dwt_error', 'File gambar tidak ditemukan di server.');
        }

        $result = $this->dwt->decode($imagePath);

        if (! $result['success']) {
            return back()->with('dwt_error', 'Decode DWT gagal: ' . $result['error']);
        }

        return back()->with([
            'dwt_decoded_message' => $result['message'],
            'dwt_decoded_id'      => $dwt->id,
        ]);
    }

    /**
     * GET /dwt/{dwt}/download — Download gambar hasil DWT.
     */
    public function download(Dwt $dwt): mixed
    {
        $disk = Storage::disk('public');

        if (! $disk->exists($dwt->gambar)) {
            return back()->with('dwt_error', 'File gambar tidak ditemukan.');
        }

        $filename = 'dwt_stegano_' . basename($dwt->gambar);
        $fullPath = $disk->path($dwt->gambar);
        $mimeType = mime_content_type($fullPath) ?: 'image/png';

        return response()->streamDownload(function () use ($disk, $dwt) {
            echo $disk->get($dwt->gambar);
        }, $filename, [
            'Content-Type' => $mimeType,
        ]);
    }

    /**
     * DELETE /dwt/{dwt} — Hapus gambar DWT.
     */
    public function destroy(Dwt $dwt): RedirectResponse
    {
        $dwt->deleteGambar();
        $dwt->delete();

        return back()->with('dwt_success', 'Gambar berhasil dihapus.');
    }
}
