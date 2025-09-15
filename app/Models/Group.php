<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
     protected $table = 'groups';

    // Primary key (default sudah id)
    protected $primaryKey = 'id';

    // Kolom yang bisa diisi
    protected $fillable = [
        'name',
        'description',
    ];

    // Jika tabel tidak punya created_at & updated_at
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */
    public function users()
    {
        return $this->belongsToMany(User::class, 'users_groups', 'group_id', 'user_id');
    }
}
