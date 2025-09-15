<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'table_region';

    protected $primaryKey = 'f_id';
    protected $fillable = [
        'f_account_id',
        'f_region_name',
        'f_region_aktif',
    ];

    public $timestamps = false; // karena tidak ada kolom created_at dan updated_at
}
