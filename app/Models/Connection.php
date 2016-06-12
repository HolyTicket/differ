<?php
namespace App\Models;

use App\Scopes\UserScope;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Auth;
use Crypt;

/**
 * Class Connection
 * The Connection Eloquent Model
 * @package App\Models
 */
class Connection extends Eloquent
{
    /**
     * Contains the database attributes that are mass assignable
     * @var array
     */
    protected $fillable = [
        'name', 'host', 'username', 'password', 'database_name', 'user_id'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        // Call the constructor
        parent::boot();

        // When saving a connection, add the user_id and remove the password when empty (empty password = not changed)
        static::saving(function($connection){
            $connection->user_id = Auth::id();
            if(empty($connection->password)) {
                unset($connection->password);
            }
        });

        // Add the UserScope, which makes sure you only see your own records
        static::addGlobalScope(new UserScope);
    }

    /**
     * Password accessor, AES decrypts the password before returning it.
     * @param $password
     * @return string
     */
    public function getPasswordAttribute($password) {
        // Decrypt the password. When an exception is thrown, return an empty string.
        try {
            // Decrypt the password
            return Crypt::decrypt($password);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return '';
        }
    }

    /**
     * Username accessor, AES decrypts the username before returning it.
     * @param $username
     * @return string
     */
    public function getUsernameAttribute($username) {
        // Decrypt the username. When an exception is thrown, return an empty string.
        try {
            return Crypt::decrypt($username);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return '';
        }
    }

    /**
     * Username mutator, AES encrypts the username before saving it.
     * @param $password the plain (unencrypted) password
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Crypt::encrypt($password);
    }

    /**
     * Username mutator, AES encrypts the username before saving it.
     * @param $username the plain (unencrypted) username
     */
    public function setUsernameAttribute($username)
    {
        $this->attributes['username'] = Crypt::encrypt($username);
    }
}