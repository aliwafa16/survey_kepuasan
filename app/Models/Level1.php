<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level1 extends Model
{
    protected $table = 'table_level_position1';
    protected $primaryKey = 'f_id';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'f_account_id',
        'f_position_desc',
        'f_token',
        'f_total_min',
        'f_aktif',
    ];
}
