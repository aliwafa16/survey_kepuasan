<?php

namespace App\Models;

use App\Models\Level2;
use Illuminate\Database\Eloquent\Model;

class Level3 extends Model
{
    protected $table = 'table_level_position3'; // nama tabel di database

    protected $primaryKey = 'f_id'; // asumsi f_id adalah primary key

    public $timestamps = false; // kalau tidak ada created_at dan updated_at

    protected $fillable = [
        'f_account_id',
        'f_id2',
        'f_position_desc',
        'f_total',
        'f_total_min',
        'f_aktif'
    ];

    public function relasi_level2(){
        return $this->belongsTo(Level2::class, 'f_id2', 'f_id');
    }
}
