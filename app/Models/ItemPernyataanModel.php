<?php

namespace App\Models;

use App\Models\Variabel;
use Illuminate\Database\Eloquent\Model;

class ItemPernyataanModel extends Model
{
    // Nama tabel
    protected $table = 't_item_pernyataan';

    // Primary key
    protected $primaryKey = 'f_id';

    // Kolom yang bisa diisi
    protected $fillable = [
        'f_kode',
        'f_item',
        'f_item_eng',
        'f_answer',
        'type',
        'f_variabel_id',
        'f_dimensi_id',
        'f_kategori',
        'free',
    ];

    // Jika tabel tidak punya created_at & updated_at
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */
    public function variabel()
    {
        return $this->belongsTo(Variabel::class, 'f_variabel_id', 'f_id');
    }
}
