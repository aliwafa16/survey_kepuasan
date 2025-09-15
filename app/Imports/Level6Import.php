<?php

namespace App\Imports;

use App\Models\Level6;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class Level6Import implements WithMultipleSheets
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
                        Level6::create([
                            'f_position_desc' => $row['level_6'],     // kolom Excel
                            'f_id5'           => $row['kode_level_5'],  // kolom Excel
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
