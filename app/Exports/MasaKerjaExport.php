<?php

namespace App\Exports;

use App\Models\MasaKerja;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MasaKerjaexport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return new Collection([]);
    }

    public function headings(): array
    {
        return ['masa_kerja'];
    }
}
