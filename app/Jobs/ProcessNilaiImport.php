<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Imports\NilaiImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessNilaiImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $tahunAjaranMatkulId;
    protected $dosenId;
    protected $userId;

    public $timeout = 300; // 5 menit timeout
    public $tries = 3; // Retry 3 kali jika gagal

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $tahunAjaranMatkulId, $dosenId, $userId)
    {
        $this->filePath = $filePath;
        $this->tahunAjaranMatkulId = $tahunAjaranMatkulId;
        $this->dosenId = $dosenId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Log::info('Starting background import for user: ' . $this->userId);

            // Process import
            $import = new NilaiImport($this->tahunAjaranMatkulId, $this->dosenId);
            Excel::import($import, $this->filePath);

            // Get results
            $results = $import->getImportResults();

            // Store results in cache/session for user to retrieve
            $cacheKey = "import_results_{$this->userId}_{$this->tahunAjaranMatkulId}";
            cache()->put($cacheKey, $results, now()->addHours(1));

            // Clean up temporary file
            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
            }

            Log::info('Background import completed successfully', $results);

        } catch (\Exception $e) {
            Log::error('Background import failed: ' . $e->getMessage(), [
                'file' => $this->filePath,
                'user_id' => $this->userId,
                'trace' => $e->getTraceAsString()
            ]);

            // Store error in cache
            $cacheKey = "import_results_{$this->userId}_{$this->tahunAjaranMatkulId}";
            cache()->put($cacheKey, [
                'error' => true,
                'message' => $e->getMessage()
            ], now()->addHours(1));

            // Clean up temporary file
            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
            }

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('Import job failed permanently', [
            'file' => $this->filePath,
            'user_id' => $this->userId,
            'error' => $exception->getMessage()
        ]);

        // Clean up temporary file
        if (Storage::exists($this->filePath)) {
            Storage::delete($this->filePath);
        }
    }
}
