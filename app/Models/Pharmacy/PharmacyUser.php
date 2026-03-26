<?php

namespace App\Models\Pharmacy;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * PharmacyUser Model
 *
 * Represents a pharmacy user account with authentication capabilities.
 * Extends the Authenticatable class to provide built-in authentication features.
 *
 * @property string $name The name of the pharmacy user
 * @property string $email The email address of the pharmacy user
 * @property string $password The hashed password of the pharmacy user
 *
 * @package App\Models\Pharmacy
 */
class PharmacyUser extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pharmacy_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];
}
