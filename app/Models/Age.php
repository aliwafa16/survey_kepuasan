<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Age extends Model
{
    protected $table = 'table_age';
    protected $primaryKey = 'f_id';
    public $timestamps = false;

    protected $fillable = [
        'f_account_id',
        'f_age_desc',
        'f_age_aktif',
    ];
}
