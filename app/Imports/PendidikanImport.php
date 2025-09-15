<?php

namespace App\Imports;

use App\Models\Pendidikan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PendidikanImport implements ToCollection,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            Pendidikan::create([
                'f_name' => $row['pendidikan'], // Sesuaikan dengan nama kolom di Excel
                'f_account_id' =>Auth::user()->f_account_id ,
                'f_kode' => 'ID',
                'f_aktif' => 1,
            ]);
        }
    }
}
