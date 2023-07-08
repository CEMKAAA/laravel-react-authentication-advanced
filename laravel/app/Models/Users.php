<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'users';

    protected $primaryKey = 'id';

    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile',
        'verification_token_expires_at',
        'verificationToken',
        'status',
        'is_verified',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}

