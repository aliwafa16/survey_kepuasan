<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountClient extends Model
{
    protected $table = 't_account';
    protected $primaryKey = 'f_account_id';
    public $timestamps = false; // karena kamu pakai `f_account_created_on` bukan `created_at`

    const CREATED_AT = 'f_account_created_on';
    const UPDATED_AT = 'f_account_updated_on';

    protected $fillable = [
        'f_account_name',
        'f_account_contact',
        'f_account_phone',
        'f_account_email',
        'f_account_noacc',
        'f_account_logo',
        'f_account_created_by',
        'f_account_updated_by',
        'f_account_status',
        'f_account_token',
        'is_corporate'
    ];
}
