<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Group;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

protected $table = 'users';

    // Primary key
    protected $primaryKey = 'id';

    // Kolom yang bisa diisi
    protected $fillable = [
        'ip_address',
        'username',
        'password',
        'email',
        'activation_selector',
        'activation_code',
        'forgotten_password_selector',
        'forgotten_password_code',
        'forgotten_password_time',
        'remember_selector',
        'remember_code',
        'created_on',
        'updated_on',
        'last_login',
        'active',
        'first_name',
        'last_name',
        'company',
        'phone',
        'f_account_id',
    ];

    // Karena kolom timestamp custom, bukan created_at/updated_at default
    public $timestamps = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function groups()
{
    return $this->belongsToMany(
        Group::class,       // model tujuan
        'users_groups',     // nama tabel pivot
        'user_id',          // foreign key di tabel pivot ke users
        'group_id'          // foreign key di tabel pivot ke groups
    );
}
}
