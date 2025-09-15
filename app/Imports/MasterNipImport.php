<?php

namespace App\Imports;

use App\Models\MasterNip;
use App\Models\TrnSurvey;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MasterNipImport implements ToModel,WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
           // Cek apakah kombinasi id_account dan nip sudah ada
        $exists = MasterNip::where('id_account', Auth::user()->f_account_id)
            ->where('nip', $row['nip'])
            ->exists();

        if ($exists) {
            return null; // lewati insert
        }

        return new MasterNip([
            'id_account'           => Auth::user()->f_account_id ?? 0,
            'f_name'           => $row['name'] ?? NULL,
            // 'f_email'           => $row['email'] ?? NULL,
            'nip'                  => $row['nip'] ?? NULL,
            'tanggal_lahir'        => $row['tanggal_lahir'] ?? NULL,
            'f_respon'             => $row['respon'] ?? NULL ,
            'f_survey_valid'       => $row['survey_valid'] ?? "no",
            'f_survey_date'        => $row['survey_date'] ?? NULL,
            'f_type'               => $row['type'] ?? NULL,
            'f_gender'             => $row['gender'] ?? NULL,
            'f_age'                => $row['age'] ?? NULL,
            'f_length_of_service'  => $row['length_of_service'] ?? NULL,
            'f_region'             => $row['region'] ?? NULL,
            'f_level_of_work'      => $row['level_of_work'] ?? NULL,
            'f_level1'             => $row['level1'] ?? NULL,
            'f_level2'             => $row['level2'] ?? NULL,
            'f_level3'             => $row['level3'] ?? NULL,
            'f_level4'             => $row['level4'] ?? NULL,
            'f_level5'             => $row['level5'] ?? NULL,
            'f_level6'             => $row['level6'] ?? NULL,
            'f_level7'             => $row['level7'] ?? NULL,
            'f_custom1'            => $row['custom1'] ?? NULL,
            'f_custom2'            => $row['custom2'] ?? NULL,
            'f_custom3'            => $row['custom3'] ?? NULL,
            'f_custom4'            => $row['custom4'] ?? NULL,
            'f_pendidikan'         => $row['pendidikan'] ?? NULL,
        ]);
    }
}
