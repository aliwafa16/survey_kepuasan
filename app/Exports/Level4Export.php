<?php

namespace App\Exports;

use App\Models\Level3;
use App\Models\Level4;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Level4Export implements WithMultipleSheets
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
                    return ['kode_level_3', 'level_4'];
                }

                public function title(): string
                {
                    return 'Template Level 4';
                }
            },

            new class implements FromCollection, WithHeadings, WithTitle {
                public function collection()
                {
                    return Level3::select('f_id','f_position_desc')->where('f_account_id', Auth::user()->f_account_id)->get();
                }

                public function headings(): array
                {
                    return ['kode','level_3'];
                }

                public function title(): string
                {
                    return 'Data Level 3';
                }
            }
        ];
    }
}
