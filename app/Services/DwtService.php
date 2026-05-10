<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

/**
 * DwtService
 * ==========
 * Service untuk menjalankan script Python DWT steganography
 * menggunakan Symfony Process (aman dari command injection).
 */
class DwtService
{
    private string $pythonBin;
    private string $scriptPath;
    private int    $timeout;

    public function __construct()
    {
        $this->pythonBin  = config('steganography.python_bin', 'python');
        $this->scriptPath = base_path('python/dwt_steganography.py');
        $this->timeout    = (int) config('steganography.timeout', 60);
    }

    /**
     * Encode pesan ke dalam gambar menggunakan DWT Haar.
     */
    public function encode(string $inputPath, string $outputPath, string $message): array
    {
        if (! file_exists($inputPath)) {
            return $this->errorResult("File gambar tidak ditemukan: {$inputPath}");
        }

        if (trim($message) === '') {
            return $this->errorResult("Pesan tidak boleh kosong.");
        }

        $command = [
            $this->pythonBin,
            $this->scriptPath,
            'encode',
            $inputPath,
            $outputPath,
            $message,
        ];

        return $this->runProcess($command, 'dwt-encode');
    }

    /**
     * Decode pesan tersembunyi dari gambar DWT.
     */
    public function decode(string $imagePath): array
    {
        if (! file_exists($imagePath)) {
            return $this->errorResult("File gambar tidak ditemukan: {$imagePath}");
        }

        $command = [
            $this->pythonBin,
            $this->scriptPath,
            'decode',
            $imagePath,
        ];

        $result = $this->runProcess($command, 'dwt-decode');

        if (! $result['success']) {
            return $result;
        }

        $output = trim($result['output']);
        if (str_starts_with($output, 'DECODED:')) {
            $result['message'] = substr($output, strlen('DECODED:'));
        } else {
            $result['message'] = $output;
        }

        return $result;
    }

    /**
     * Jalankan proses Python.
     */
    private function runProcess(array $command, string $context): array
    {
        try {
            $process = new Process($command);
            $process->setTimeout($this->timeout);
            $process->run();

            $stdout = trim($process->getOutput());
            $stderr = trim($process->getErrorOutput());

            if (! $process->isSuccessful()) {
                $errorMsg = $stderr ?: "Proses Python gagal (exit code: {$process->getExitCode()})";
                Log::error("DwtService [{$context}] FAILED", [
                    'exit_code' => $process->getExitCode(),
                    'stderr'    => $stderr,
                ]);
                return $this->errorResult($errorMsg);
            }

            Log::info("DwtService [{$context}] SUCCESS", ['output' => $stdout]);

            return [
                'success' => true,
                'output'  => $stdout,
                'error'   => '',
                'message' => '',
            ];
        } catch (\Exception $e) {
            Log::error("DwtService [{$context}] EXCEPTION", ['error' => $e->getMessage()]);
            return $this->errorResult("Terjadi kesalahan: " . $e->getMessage());
        }
    }

    private function errorResult(string $errorMessage): array
    {
        return [
            'success' => false,
            'output'  => '',
            'error'   => $errorMessage,
            'message' => '',
        ];
    }
}
