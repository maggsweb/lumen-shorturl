<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * @method static create($array)
 * @method static where($column, $value)
 * @method static byShortUrl($string)
 */
class Link extends Model
{
//    /**
//     * Authenticated User
//     *
//     * @var Authenticatable|null
//     */
//    protected $authUser;

    protected $fillable = [
        'short',
        'long',
        'user_id'
    ];

//    public function __construct(array $attributes = [])
//    {
//        parent::__construct($attributes);
//        $this->authUser = Auth()->user();
//    }

    /**
     * User relation
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

//    public function getRouteKeyName()
//    {
//        return 'short';
//    }

    /**
     * The current FQ domain string
     * @return string
     */
    private function getDomain(): string
    {
        return env('APP_URL', '');
    }

    /**
     * @param Builder $builder
     * @param string $short_url
     * @return Builder
     */
    public function scopeByShortUrl(Builder $builder, string $short_url): Builder
    {
        $authUserId = Auth()->user()->getAuthIdentifier();

        return $builder
            ->join('users', 'links.user_id', '=', 'users.id')
            ->where('short', $short_url)
            ->where('users.id', $authUserId)
            ->where('users.status', DB::raw("'Active'"));
    }

    /**
     * Return selected fields only
     * @return array
     */
    public function toArray(): array
    {
        return [
            'short'   => $this->short,
            'full'    => $this->getDomain() . '/' . $this->short,
            'long'    => $this->long,
            'created' => $this->created_at
        ];
    }

}
