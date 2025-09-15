<?php

namespace App\Models;

use App\Models\Level1;
use Illuminate\Database\Eloquent\Model;

class Level2 extends Model
{
    protected $table = 'table_level_position2'; // nama tabel di database

    protected $primaryKey = 'f_id'; // jika primary key bukan 'id'

    public $timestamps = false; // jika tabel tidak memiliki kolom created_at dan updated_at

    protected $fillable = [
        'f_account_id',
        'f_id1',
        'f_position_desc',
        'f_total',
        'f_total_min',
        'f_aktif'
    ];


    public function relasi_level1(){
        return $this->belongsTo(Level1::class, 'f_id1', 'f_id');
    }
}
