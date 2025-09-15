<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // Default table name is 'settings' (sesuai nama model Setting)
    // Jika nama tabel berbeda, tambahkan: protected $table = 'nama_tabel';

    protected $table =  'corporate_profiles';

    protected $fillable = [
        'logo',
        'banner',
        'color_primary',
        'color_secondary',
        'tagline',
        'id_corporate',
    ];
//

    public $timestamps = true;
}
