<?php

namespace App\Models;

use App\Models\Level1;
use App\Models\Level2;
use App\Models\Level3;
use Illuminate\Database\Eloquent\Model;

class TrnSurvey extends Model
{
    protected $table = 'trn_survey_empex'; // ganti sesuai nama tabel kamu

    protected $primaryKey = 'f_id';

    public $timestamps = false; // karena kamu pakai `f_survey_created_on` bukan created_at

protected $fillable = [
        'f_account_id',
        'f_event_id',
        'f_survey_username',
        'f_email',
        'f_umur',
        'tgl_lahir',
        'f_no_telp',
        'f_jenis_kelamin',
        'f_survey_password',
        'f_survey_note',
        'f_survey',
        'f_survey_valid',
        'f_report_status',
        'f_pendidikan',
        'f_level1',
        'f_level2',
        'f_level3',
        'f_level4',
        'f_level5',
        'f_level6',
        'f_level7',
        'level_work',
        'negara',
        'provinsi',
        'f_bahasa',
        'f_report',
        'f_report_type',
        'f_voice_10',
        'f_voice_45',
        'f_voice_65',
        'f_survey_created_on',
        'f_survey_created_by',
        'f_survey_updated_on',
        'f_survey_updated_by',
        'status_mail',
        'f_ip_address',
        'f_flag_afiliasi',
        'f_contact_reg_id',
        'f_contact_reg_detail_id',
        'created_by',
        'f_job_voice_10',
        'f_job_voice_45',
        'f_job_voice_65',
        'f_generated_time_voice',
        'f_voucher_code',
        'event_voucher_id',
        'f_status_bayar',
        'f_device_no',
        'id_agent_2u',
        'f_updated_send',
        'f_corporate_id',
        'f_from_corporate_id',
        'f_age',
        'f_gender',
        'f_length_of_service',
        'f_level_of_work',
        'f_region',
        'f_pendidikan_account',
        'f_nip',
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

public function levelwork()
{
    return $this->belongsTo(LevelWork::class, 'f_level_of_work', 'f_id');
}
}
