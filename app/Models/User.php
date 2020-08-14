<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class User extends Authenticatable
{
    use Notifiable;
    
    public const TYPE_EMPLOYEE = 'employee';
    public const TYPE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'organisation_id', 'type'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    
    public function organisation() : ?BelongsTo
    {
        return $this->belongsTo('App\Models\Organisation', 'organisation_id');
    }
    
    public function isAdmin() : bool
    {
        return $this->type === 'admin';
    }
    
    public static function getTypes() : array
    {
        return [self::TYPE_EMPLOYEE, self::TYPE_ADMIN];
    }
}
