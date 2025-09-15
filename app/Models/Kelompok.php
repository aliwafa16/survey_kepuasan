<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelompok extends Model
{
    use HasFactory;

    // Nama tabel
    protected $table = 't_event';

    // Primary key
    protected $primaryKey = 'f_event_id';

    // Jika primary key bukan auto increment
    public $incrementing = false;

    // Jika primary key menggunakan tipe string, ubah tipe primaryKey ini
    // protected $keyType = 'string';

    // Nonaktifkan timestamp (created_at, updated_at)
    public $timestamps = false;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'f_event_id',
        'f_event_name',
        'f_event_start',
        'f_event_start_time',
        'f_event_end',
        'f_event_end_time',
        'f_event_status',
        'f_event_survey',
        'f_account_id',
        'f_event_click',
        'f_event_kode',
        'f_event_type',
        'f_event_min_respon',
        'f_event_respon',
        'f_event_created_on',
        'f_event_created_by',
        'f_event_updated_on',
        'f_event_updated_by',
        'f_training_schedule_id',
        'f_trainer_id',
        'f_report_type',
        'f_kuota',
        'f_sudah_isi',
        'id_account_ai',
    ];
}

