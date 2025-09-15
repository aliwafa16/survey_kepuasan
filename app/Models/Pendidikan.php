<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pendidikan extends Model
{
    protected $table = 'table_pendidikan_account';

    protected $primaryKey = 'f_id';

    protected $fillable = [
        'f_account_id',
        'f_kode',
        'f_name',
        'f_aktif',
    ];

    public $timestamps = false;
}
