<?php

namespace App\Exports;

use App\Models\Level4;
use App\Models\Level5;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Level5Export implements WithMultipleSheets
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
                    return ['kode_level_4', 'level_5'];
                }

                public function title(): string
                {
                    return 'Template Level 5';
                }
            },

            new class implements FromCollection, WithHeadings, WithTitle {
                public function collection()
                {
                    return Level4::select('f_id','f_position_desc')->get();
                }

                public function headings(): array
                {
                    return ['kode','level_4'];
                }

                public function title(): string
                {
                    return 'Data Level 5';
                }
            }
        ];
    }
   
}
