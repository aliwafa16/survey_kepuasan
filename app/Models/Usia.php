<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usia extends Model
{
    protected $table = 'table_age';

    protected $primaryKey = 'f_id';

    protected $fillable = [
        'f_account_id',
        'f_age_desc',
        'f_age_aktif',
    ];

    public $timestamps = false; // karena tidak ada created_at dan updated_at
}
