<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class LevelWork extends Model
{
    protected $table = 'table_level_work';

    protected $primaryKey = 'f_id'; // asumsinya f_id adalah primary key'
    public $timestamps = false; // jika tabel tidak punya created_at dan updated_at

    protected $fillable = [
        'f_account_id',
        'f_level_work_desc',
        'f_aktif',

    ];

}
