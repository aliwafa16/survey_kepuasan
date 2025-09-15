<?php

namespace App\Imports;

use App\Models\Wilayah;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WilayahImport implements ToCollection,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            Wilayah::create([
                'f_region_name' => $row['wilayah'], // Sesuaikan dengan nama kolom di Excel
                'f_account_id' =>Auth::user()->f_account_id ,
                'f_region_aktif' => 1,
            ]);
        }
    }
}
