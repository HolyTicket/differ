<?php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @package App
 */
class User extends Authenticatable
{
    /**
     * @var array mass assignable fields
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * @var array hidden fields
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}