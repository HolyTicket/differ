<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Auth;
use Crypt;

class Connection extends Eloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'host', 'username', 'password', 'database_name', 'user_id'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function($connection){
            $connection->user_id = Auth::id();
            if(empty($connection->password)) {
                unset($connection->password);
            }
        });
    }

    public function getPasswordAttribute($password) {
        try {
            return Crypt::decrypt($password);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return '';
        }
    }

    public function getUsernameAttribute($username) {
        try {
            return Crypt::decrypt($username);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return '';
        }
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Crypt::encrypt($password);
    }

    public function setUsernameAttribute($username)
    {
        $this->attributes['username'] = Crypt::encrypt($username);
    }

}
