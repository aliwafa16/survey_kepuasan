<?php

namespace App\Models;

use App\Models\Level4;
use Illuminate\Database\Eloquent\Model;

class Level5 extends Model
{
    protected $table = 'table_level_position5';

    protected $primaryKey = 'f_id'; // asumsinya ini adalah primary key

    public $timestamps = false; // kalau tidak ada created_at dan updated_at

    protected $fillable = [
        'f_account_id',
        'f_id4',
        'f_position_desc',
        'f_total',
        'f_total_min',
        'f_aktif'
    ];


    public function relasi_level4(){
        return $this->belongsTo(Level4::class, 'f_id4', 'f_id');
    }
}
