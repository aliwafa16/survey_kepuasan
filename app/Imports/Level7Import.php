<?php

namespace App\Imports;

use App\Models\Level7;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Level7Import implements WithMultipleSheets
{
    /**
    * @param Collection $collection
    */
    public function sheets(): array
    {
        return [
            0 => new class implements ToCollection, WithHeadingRow {
                public function collection(Collection $rows)
                {
                    foreach ($rows as $row) {
                        Level7::create([
                            'f_position_desc' => $row['level_7'],     // kolom Excel
                            'f_id6'           => $row['kode_level_6'],  // kolom Excel
                            'f_account_id'    => Auth::user()->f_account_id,
                            'f_aktif'         => 1,
                        ]);
                    }
                }
            },

            // Sheet 1 (index ke-1) tidak perlu ditangani, atau bisa buat handler dummy
        ];
    }
}
