<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static create($array)
 * @method static where($column, $value)
 * @method static byShortUrl($string)
 * @method static byLongUrl($string)
 * @method static byUser()
 */
class Link extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'short',
        'long',
        'user_id',
    ];

    protected $dates = [
        'created_at',
    ];

    /**
     * Retrieve a Link via ShortCode.
     *
     * @param $short_code
     *
     * @return Link | null
     */
    public static function retrieve($short_code): ?Link
    {
        return Link::byShortUrl($short_code)->first();
    }

    /**
     * Get Long URL.
     *
     * @return string|null
     */
    public function getLongUrl(): ?string
    {
        return $this->long ?? null;
    }

    /**
     * Get Short Code.
     *
     * @return string|null
     */
    public function getShortCode(): ?string
    {
        return $this->short ?? null;
    }

    /**
     * Get created Date.
     *
     * @return string|null
     */
    public function getCreatedDate(): ?string
    {
        return $this->created_at ?? null;
    }

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
     * Scope by Short Code.
     *
     * @param Builder $builder
     * @param string  $short_code
     *
     * @return Builder
     */
    public function scopeByShortUrl(Builder $builder, string $short_code): Builder
    {
        return $builder->where('short', $short_code);
    }

    /**
     * Scope by Long URL.
     *
     * @param Builder $builder
     * @param string  $long_url
     *
     * @return Builder
     */
    public function scopeByLongUrl(Builder $builder, string $long_url): Builder
    {
        return $builder->where('long', $long_url);
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
            'short'   => $this->getShortCode(),
            'long'    => $this->getLongUrl(),
            'full'    => $this->getDomain().'/'.$this->getShortCode(),
            'created' => $this->getCreatedDate(),
        ];
    }
}
