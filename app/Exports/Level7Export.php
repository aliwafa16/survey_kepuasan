<?php

namespace App\Exports;

use App\Models\Level6;
use App\Models\Level7;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Level7Export implements WithMultipleSheets
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function sheets(): array
    {
        return [
            new class implements FromCollection, WithHeadings, WithTitle {
                public function collection()
                {
                    return new Collection([]);
                }

                public function headings(): array
                {
                    return ['kode_level_6', 'level_7'];
                }

                public function title(): string
                {
                    return 'Template Level 7';
                }
            },

            new class implements FromCollection, WithHeadings, WithTitle {
                public function collection()
                {
                    return Level6::select('f_id','f_position_desc')->get();
                }

                public function headings(): array
                {
                    return ['kode','level_6'];
                }

                public function title(): string
                {
                    return 'Data Level 6';
                }
            }
        ];
    }
}
