<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TingkatPekerjaan extends Model
{
    protected $table = 'table_level_work';

    protected $primaryKey = 'f_id';

    protected $fillable = [
        'f_account_id',
        'f_levelwork_desc',
        'f_aktif',
    ];

    public $timestamps = false; // karena tidak ada kolom created_at dan updated_at
}
