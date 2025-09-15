<?php

namespace App\Models;

use App\Models\Level6;
use Illuminate\Database\Eloquent\Model;

class Level7 extends Model
{
    protected $table = 'table_level_position7';

    protected $primaryKey = 'f_id'; // asumsinya f_id adalah primary key'
    public $timestamps = false; // jika tabel tidak punya created_at dan updated_at

    protected $fillable = [
        'f_id',
        'f_account_id',
        'f_id6',
        'f_position_desc',
        'f_total',
        'f_total_min',
        'f_aktif',
    ];

    public function relasi_level6(){
        return $this->belongsTo(Level6::class, 'f_id6', 'f_id');
    }

}
