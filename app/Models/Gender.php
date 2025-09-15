<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $table = 'table_gender';
    protected $primaryKey = 'f_gender_id';
    public $timestamps = false;

    protected $fillable = [
        'f_gender_name',
        'f_account_id',
        'f_create_date',
        'f_create_by',
        'f_update_date',
        'f_update_by',
    ];
}
