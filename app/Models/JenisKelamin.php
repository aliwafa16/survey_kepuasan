<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisKelamin extends Model
{
    protected $table = 'table_gender';

    protected $primaryKey = 'f_gender_id'; // Asumsinya ini adalah primary key      // Kalau f_gender_id bukan auto-increment
    protected $keyType = 'string';         // Karena ID-nya berupa string (bukan integer)

    protected $fillable = [
        'f_gender_name',
        'f_account_id',
        'f_create_date',
        'f_create_by',
        'f_update_date',
        'f_update_by',
    ];

    public $timestamps = false; // Karena pakai manual create_date & update_date
}
