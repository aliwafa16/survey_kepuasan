<?php

namespace App\Jobs;

use App\Imports\MasterNipImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ImportMasterNipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filename;

    public function __construct($filename)
    {
        $this->filename = $filename; // relatif dari storage/app


        // dd($filename);
    }

    public function handle()
    {

            $fullPath = storage_path('app/private/imports/' . $this->filename);

    // Debug terminal
    echo "[DEBUG] Importing file: " . $fullPath . PHP_EOL;

    if (!file_exists($fullPath)) {
        \Log::error('File tidak ditemukan: ' . $fullPath);
        throw new \Exception('File tidak ditemukan: ' . $fullPath);
    }

    // Import dengan tipe eksplisit (XLSX)
    Excel::import(new MasterNipImport, $fullPath, null, \Maatwebsite\Excel\Excel::XLSX);

    // Hapus file setelah import
    Storage::delete('private/imports/' . $this->filename);
        
        // (Optional) Hapus file setelah selesai
        // Storage::delete($this->filePath);
    }
}
