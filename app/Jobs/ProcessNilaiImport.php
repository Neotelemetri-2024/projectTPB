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
use Illuminate\Support\Facades\Cache;

class ProcessNilaiImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobId;
    public $filePath;
    public $dosenId;
    public $tahunAjaranMatkulId;
    public $relatedClasses;
    public $timeout = 3600;

    /**
     * Create a new job instance.
     */
    public function __construct($jobId, $filePath, $dosenId, $tahunAjaranMatkulId, $relatedClasses)
    {
        $this->jobId = $jobId;
        $this->filePath = $filePath;
        $this->dosenId = $dosenId;
        $this->tahunAjaranMatkulId = $tahunAjaranMatkulId;
        $this->relatedClasses = $relatedClasses;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Log::info("Starting ProcessNilaiImport for jobId: {$this->jobId}");
            
            // Initializing Cache
            Cache::put('import_progress_' . $this->jobId . '_processed', 0, 3600);
            
            // Process import
            $import = new NilaiImport($this->tahunAjaranMatkulId, $this->dosenId, $this->relatedClasses, $this->jobId);
            Excel::import($import, $this->filePath);
            
            // The result is already cached by NilaiImport
            Log::info("ProcessNilaiImport finished for jobId: {$this->jobId}");

        } catch (\Exception $e) {
            Log::error('Background import failed: ' . $e->getMessage(), [
                'job_id' => $this->jobId,
                'trace' => $e->getTraceAsString()
            ]);

            // Store error in cache
            Cache::put('import_result_' . $this->jobId, [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                'errors' => [],
                'total_success' => 0,
                'total_errors' => 1
            ], 3600);

            throw $e;
        } finally {
            // Clean up temporary file
            if (file_exists($this->filePath)) {
                @unlink($this->filePath);
            }
        }
    }
}
