<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Auth\Authorizable;

/**
 * @method static byToken($string)
 */

class User extends Model implements AuthenticatableContract, AuthorizableContract
{

    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ ];

    /**
     * Scope a valid User
     *
     * @param Builder $builder
     * @param $token
     * @return Builder
     */
    public function scopeByToken(Builder $builder, $token)
    {
        return $builder
            ->where('token', $token)
            ->where('status', DB::raw("'Active'"));
    }

}
