<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserGroups extends Model
{
     protected $table = 'users_groups';

    // Primary key (kalau bukan id, tapi disini pakai id → default sudah benar)
    protected $primaryKey = 'id';

    // Kolom yang bisa diisi (mass assignable)
    protected $fillable = [
        'user_id',
        'group_id',
    ];

    // Jika tidak pakai timestamps created_at & updated_at
    public $timestamps = false;


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
