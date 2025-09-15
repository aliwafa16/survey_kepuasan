<?php

namespace App\Imports;

use App\Models\MasaKerja;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MasaKerjaImport implements ToCollection,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            MasaKerja::create([
                'f_service_desc' => $row['masa_kerja'], // Sesuaikan dengan nama kolom di Excel
                'f_account_id' =>Auth::user()->f_account_id ,
                'f_service_aktif' => 1,
            ]);
        }
    }
}
