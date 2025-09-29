<?php

namespace App\Models;

use App\Models\Usia;
use App\Models\Level1;
use App\Models\Level2;
use App\Models\Level3;
use App\Models\Level4;
use App\Models\Level5;
use App\Models\Wilayah;
use App\Models\LevelWork;
use App\Models\MasaKerja;
use App\Models\Pendidikan;
use App\Models\JenisKelamin;
use App\Models\AccountClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrnSurvey extends Model
{
    use HasFactory;

    protected $table = 'trn_survey_empex';
    protected $primaryKey = 'f_id';
    public $timestamps = false; // karena field created_at/updated_at tidak standar

    protected $fillable = [
        'f_account_id',
        'f_event_id',
        'f_survey_username',
        'f_survey_email',
        'f_survey_telp',
        'f_survey_password',
        'f_survey_demographic',
        'f_survey',
        'f_survey_qopen',
        'f_survey_valid',
        'f_gender',
        'f_age',
        'f_length_of_service',
        'f_region',
        'f_level_of_work',
        'f_pendidikan',
        'f_level1',
        'f_level2',
        'f_level3',
        'f_level4',
        'f_level5',
        'f_level6',
        'f_level7',
        'f_custom1',
        'f_custom2',
        'f_custom3',
        'f_custom4',
        'f_custom5',
        'f_custom6',
        'f_custom7',
        'f_custom8',
        'f_custom9',
        'f_custom10',
        'f_survey_created_on',
        'f_survey_created_by',
        'f_survey_updated_on',
        'f_survey_updated_by',
        'f_ip_address',
        'f_variabel_id',
        'free',
        'f_name_business',
        'f_established',
        'f_master_generasi_id',
        'f_master_keterlibatan_id',
        'f_master_tahunlahir_id',
        'f_master_totalemployee_id',
        'f_master_totalkeluarga_id',
        'role_responden',
        'f_client_id',
    ];

    public function level1()
{
    return $this->belongsTo(Level1::class, 'f_level1', 'f_id');
}
public function level2()
{
    return $this->belongsTo(Level2::class, 'f_level2', 'f_id');
}
public function level3()
{
    return $this->belongsTo(Level3::class, 'f_level3', 'f_id');
}

public function level4()
{
    return $this->belongsTo(Level4::class, 'f_level4', 'f_id');
}

public function level5()
{
    return $this->belongsTo(Level5::class, 'f_level5', 'f_id');
}

public function relasi_gender(){
        return $this->belongsTo(JenisKelamin::class, 'f_gender', 'f_gender_id');
    }

        public function relasi_umur(){
        return $this->belongsTo(Usia::class, 'f_age', 'f_id');
    }

    public function relasi_masa_kerja(){
        return $this->belongsTo(MasaKerja::class, 'f_length_of_service', 'f_id');
    }

    public function relasi_wilayah(){
        return $this->belongsTo(Wilayah::class, 'f_region', 'f_id');
    }
    
    public function relasi_jabatan(){
        return $this->belongsTo(LevelWork::class, 'f_level_of_work', 'f_id');
    }

        public function relasi_pendidikan(){
        return $this->belongsTo(Pendidikan::class, 'f_pendidikan', 'f_id');
    }

        public function account()
    {
        return $this->belongsTo(AccountClient::class, 'f_account_id');
    }


            public function events()
    {
        return $this->belongsTo(EventClient::class, 'f_event_id' );
    }
}
