<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterNip extends Model
{
    use HasFactory;

    protected $table = 't_master_nip';

    protected $primaryKey = 'id';

    public $timestamps = false; // jika tidak ada created_at dan updated_at

    protected $fillable = [
        'id_account',
        'nip',
        'f_email',
        'f_name',
        'tanggal_lahir',
        'f_respon',
        'f_survey_valid',
        'f_survey_date',
        'f_type',
        'f_gender',
        'f_age',
        'f_length_of_service',
        'f_region',
        'f_level_of_work',
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
        'f_pendidikan',
    ];


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

    public function relasi_level1(){
        return $this->belongsTo(Level1::class, 'f_level1', 'f_id');
    }

    public function relasi_level2(){
        return $this->belongsTo(Level2::class, 'f_level2', 'f_id');
    }

    public function relasi_level3(){
        return $this->belongsTo(Level3::class, 'f_level3', 'f_id');
    }

    public function relasi_level4(){
        return $this->belongsTo(Level4::class, 'f_level4', 'f_id');
    }
    
    public function relasi_level5(){
        return $this->belongsTo(Level5::class, 'f_level5', 'f_id');
    }
        public function relasi_level6(){
        return $this->belongsTo(Level6::class, 'f_level6', 'f_id');
    }

            public function relasi_level7(){
        return $this->belongsTo(Level7::class, 'f_level7', 'f_id');
    }

    // Relasi ke tabel account jika ada
    public function account()
    {
        return $this->belongsTo(AccountClient::class, 'id_account', 'f_account_id');
    }
}
