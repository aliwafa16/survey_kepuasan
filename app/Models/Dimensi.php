<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dimensi extends Model
{
    use HasFactory;
    protected $table = 't_dimensi'; // Nama tabel
    protected $primaryKey = 'f_id'; // Primary key tabel
    public $incrementing = false; // Atur sesuai tipe primary key Anda, ubah menjadi true jika auto-increment
    protected $keyType = 'string'; // Ubah sesuai tipe data primary key, misal 'int' jika integer
    public $timestamps = false; // Nonaktifkan jika tabel tidak menggunakan created_at dan updated_at
    protected $fillable = [
        'f_id',
        'f_dimensi_name',
        'f_variabel_id',
        'f_sub_variabel_id',
        'hc',
        'f_name_indo',
    ];
}
