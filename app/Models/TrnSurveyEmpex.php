<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrnSurveyEmpex extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan oleh model ini
    protected $table = 'trn_survey_empex';

    // Primary key
    protected $primaryKey = 'f_id';

    // Jika primary key bukan auto-increment, tambahkan ini
    public $incrementing = false;

    // Tanggal yang akan diisi secara otomatis
    protected $dates = [
        'tgl_lahir',
        'f_survey_created_on',
        'f_survey_updated_on',
        'f_generated_time_voice',
    ];

    // Field yang dapat diisi massal
    protected $fillable = [
        'f_id',
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
    ];

    // Jika Anda ingin menonaktifkan timestamps (created_at dan updated_at)
    public $timestamps = false;
}
