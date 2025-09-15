<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Level1;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class Level1Import implements ToCollection,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        foreach ($collection as $row) {
            $level1 = Level1::create([
                'f_position_desc' => $row['level_1'], // Sesuaikan dengan nama kolom di Excel
                'f_account_id' =>Auth::user()->f_account_id ,
                // 'f_token' => $row['jumlah_kuota'],
                'f_aktif' => 1,
            ]);


            $nosjData = [
                'f_level1' => $level1->f_id,
                'f_level2' => [],
                'f_level3' => [],
                'f_level4' => [],
                'f_level5' => [],
                'f_level6' => [],
                'f_level7' => [],
            ];


            // Proses crete user
            // User::create([
            //     'username' => $row['username'],
            //     'email' => $row['email'],
            //     'password' => Hash::make($row['password']),
            //     'f_account_id' => Auth::user()->f_account_id,
            //     'nosj' => json_encode($nosjData),
            //     'active' => 1,
            //     'f_role' => 2,
            // ]);
        }
    }
}
