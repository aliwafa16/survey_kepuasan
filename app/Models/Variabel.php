<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variabel extends Model
{
    // Nama tabel
    protected $table = 't_variabel';

    // Primary key
    protected $primaryKey = 'f_id';

    // Kolom yang bisa diisi
    protected $fillable = [
        'f_variabel_name',
        'f_created_on',
        'free',
        'f_aktif',
    ];

    // Jika kolom created_at dan updated_at tidak ada
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */
    public function items()
    {
        return $this->hasMany(ItemPernyataanModel::class, 'f_variabel_id', 'f_id');
    }
}
