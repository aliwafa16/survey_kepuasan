<?php

namespace App\Exports;

use App\Models\Level5;
use App\Models\Level6;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Level6Export implements WithMultipleSheets
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
                    return ['kode_level_5', 'level_6'];
                }

                public function title(): string
                {
                    return 'Template Level 6';
                }
            },

            new class implements FromCollection, WithHeadings, WithTitle {
                public function collection()
                {
                    return Level5::select('f_id','f_position_desc')->get();
                }

                public function headings(): array
                {
                    return ['kode','level_6'];
                }

                public function title(): string
                {
                    return 'Data Level 5';
                }
            }
        ];
    }
}
