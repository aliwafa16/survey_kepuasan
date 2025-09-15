<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventClient extends Model
{
    protected $table = 't_event';
    protected $primaryKey = 'f_event_id';
    // public $incrementing = false;

    public $timestamps = true;
    const CREATED_AT = 'f_event_created_on';
    const UPDATED_AT = 'f_event_updated_on';

// Kolom yang boleh diisi mass-assignment
    protected $fillable = [
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
        'deleted_at',
        'f_training_schedule_id',
        'f_trainer_id',
        'free',
    ];

    // Cast tipe data
    protected $casts = [
        'f_event_start'            => 'date',
        'f_event_end'              => 'date',
        'f_event_created_on'       => 'datetime',
        'f_event_updated_on'       => 'datetime',
        'deleted_at'               => 'datetime',

        'f_event_status'           => 'integer',
        'f_event_survey'           => 'integer',
        'f_account_id'             => 'integer',
        'f_event_click'            => 'integer',
        'f_event_type'             => 'integer',
        'f_event_min_respon'       => 'integer',
        'f_event_respon'           => 'integer',
        'f_training_schedule_id'   => 'integer',
        'f_trainer_id'             => 'integer',
        'free'                     => 'integer',
    ];


    public function akun_client(){
       return $this->belongsTo(AccountClient::class, 'f_account_id');
    }


}
