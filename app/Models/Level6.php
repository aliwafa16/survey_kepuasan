<?php

namespace App\Models;

use App\Models\Level5;
use Illuminate\Database\Eloquent\Model;

class Level6 extends Model
{
    protected $table = 'table_level_position6';

    protected $primaryKey = 'f_id'; // jika primary key-nya f_id

    public $timestamps = false; // jika tabel tidak memiliki created_at dan updated_at

    protected $fillable = [
        'f_account_id',
        'f_id5',
        'f_position_desc',
        'f_total',
        'f_total_min',
        'f_aktif',
    ];


    public function relasi_level5(){
        return $this->belongsTo(Level5::class, 'f_id5', 'f_id');
    }


}
