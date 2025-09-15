<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'table_length_of_service';
    protected $primaryKey = 'f_id';
    public $timestamps = false;

    protected $fillable = [
        'f_account_id',
        'f_service_aktif',
        'f_service_desc',
    ];
}
