<?php

namespace App\Imports;

use App\Models\TingkatPekerjaan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TingkatPekerjaanImport implements ToCollection,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            TingkatPekerjaan::create([
                'f_levelwork_desc' => $row['tingkat_pekerjaan'], // Sesuaikan dengan nama kolom di Excel
                'f_account_id' =>Auth::user()->f_account_id ,
                'f_aktif' => 1,
            ]);
        }
    }
}
