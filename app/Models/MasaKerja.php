<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasaKerja extends Model
{
    protected $table = 'table_length_of_service';

    protected $primaryKey = 'f_id';

    protected $fillable = [
        'f_account_id',
        'f_service_desc',
        'f_service_aktif',
    ];

    public $timestamps = false; // karena tidak ada kolom created_at dan updated_at
}
