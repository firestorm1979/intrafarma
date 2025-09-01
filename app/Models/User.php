<?php

namespace App\Models;
use Illuminate\Support\Arr;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

//spatie
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function adminlte_image()
    {
        //return 'https://picsum.photos/300/300';
        
        //return asset('storage/'.$this->profile_photo_path);
        return $this->profile_photo_path ? asset('storage/' . $this->profile_photo_path) : asset('/images/gerardo_fondo.jpg');
    }

    public function adminlte_desc()
    {
        //return 'I\'m a nice guy';
        //return $this->getRoleNames();
        return Arr::first($this->getRoleNames());
    }

    public function adminlte_profile_url()
    {
        return 'profile/';
    }
}
