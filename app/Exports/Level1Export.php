<?php

namespace App\Exports;

use App\Models\Level1;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class Level1Export implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
          // Kembalikan koleksi kosong
          return new Collection([]);
    }

    public function headings(): array
    {
        return ['level_1'];
    }

}
