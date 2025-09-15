<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListLogin extends Model
{
    protected $table = 't_list_login';

    public $timestamps = false;

    protected $fillable = [
        'f_account_id',
        'f_gender',
        'f_age',
        'f_masakerja',
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
        'f_custom5',
        'f_custom6',
        'f_custom7',
        'f_custom8',
        'f_custom9',
        'f_custom10',
        'f_pendidikan',
    ];
}
