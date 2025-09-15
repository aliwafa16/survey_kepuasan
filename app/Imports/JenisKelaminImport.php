<?php

namespace App\Imports;

use App\Models\JenisKelamin;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JenisKelaminImport implements ToCollection,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            JenisKelamin::create([
                'f_gender_name' => $row['jenis_kelamin'], // Sesuaikan dengan nama kolom di Excel
                'f_account_id' =>Auth::user()->f_account_id ,
                'f_create_by' => Auth::user()->f_account_id ,
                'f_age_aktif' => 1,
            ]);
        }
    }
}
