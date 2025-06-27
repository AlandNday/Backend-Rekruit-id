<?php

namespace App\Models;

// IMPORTANT: Ensure 'use Laravel\Sanctum\HasApiTokens;' is REMOVED if it was there.
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    // IMPORTANT: Ensure 'HasApiTokens' is REMOVED from this 'use' statement.
    // It should now look like this:
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'api_token', // <-- IMPORTANT: Add this line to allow mass assignment of api_token
    ];

    /**
     * The attributes that should be hidden for serialization.
     * These attributes are hidden when the model is converted to an array or JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'api_token', // <-- IMPORTANT: Hide the api_token from public JSON responses by default
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // For Laravel 10+, this correctly hashes passwords on assignment
    ];
}