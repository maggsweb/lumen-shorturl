<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static create($array)
 * @method static where($column, $value)
 * @method static byShortUrl($string)
 * @method static byUser()
 */
class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'short',
        'long',
        'user_id',
    ];

    /**
     * User relation.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Activity relation.
     *
     * @return HasMany
     */
    public function activity(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    /**
     * The current FQ domain string.
     *
     * @return string
     */
    private function getDomain(): string
    {
        return env('APP_URL', '');
    }

    /**
     * Scope by short_url and current authenticated User.
     *
     * @param Builder $builder
     * @param string  $short_url
     *
     * @return Builder
     */
    public function scopeByShortUrl(Builder $builder, string $short_url): Builder
    {
        return $builder->where('short', $short_url);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeByUser(Builder $builder): Builder
    {
        $user_id = auth()->user()->getAuthIdentifier() ?? null;

        return $builder->where('user_id', $user_id);
    }

    /**
     * Return selected fields only.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'short'   => $this->short,
            'full'    => $this->getDomain().'/'.$this->short,
            'long'    => $this->long,
            'created' => $this->created_at,
        ];
    }
}
