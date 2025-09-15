<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalPartner extends Model
{
    use HasFactory;

    protected $table = 't_personal_partner'; // Nama tabel
    protected $primaryKey = 'id'; // Primary key

    protected $fillable = [
        'name',
        'nik',
        'groups_id',
        'email',
        'kua_id',
        'token',
        'birthdate',
        'gender',
        'status_tdna',
        'top_10_talent_value',
        'bottom_5_talent_value',
        'full_talent_value',
        'interest',
        'occupational_interest',
        'id_kecamatan',
    ];

    /**
     * Relasi ke model Kua
     */
    public function kua()
    {
        return $this->belongsTo(Kua::class, 'kua_id', 'kua_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'groups_id', 'groups_id');
    }
}
