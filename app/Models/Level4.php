<?php

namespace App\Models;

use App\Models\Level3;
use Illuminate\Database\Eloquent\Model;

class Level4 extends Model
{
    protected $table = 'table_level_position4';

    protected $primaryKey = 'f_id'; // asumsikan ini primary ke

    public $timestamps = false; // nonaktifkan timestamp default Laravel

    protected $fillable = [
        'f_account_id',
        'f_id3',
        'f_position_desc',
        'f_total',
        'f_total_min',
        'f_aktif'
    ];

    public function relasi_level3(){
        return $this->belongsTo(Level3::class, 'f_id3', 'f_id');
    }
}
