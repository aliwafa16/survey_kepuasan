<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'table_region';
    protected $primaryKey = 'f_id';
    public $timestamps = false;

    protected $fillable = [
        'f_account_id',
        'f_region_name',
        'f_region_aktif',
    ];
}
