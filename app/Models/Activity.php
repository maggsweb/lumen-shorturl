<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * @method static create($array)
 * @method static forLink($integer)
 */
class Activity extends Model
{

    protected $table = 'activity';

    protected $with = [
        'link'
    ];

    public function link(): BelongsTo
    {
        return $this->belongsTo(Link::class, 'link_id');
    }

    /**
     * Activity constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->timestamps = false;
    }

    /**
     *
     * @var array
     */
    protected $guarded = [ ];

    /**
     * Get Activity for a specific link
     *
     * @param Builder $builder
     * @param int $link_id
     * @return Builder
     */
    public function scopeForLink(Builder $builder, int $link_id): Builder
    {
        return $builder
            ->where('link_id', $link_id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Log a Redirect Link
     *
     * @param Link $link
     */
    public static function redirect(Link $link)
    {
        Activity::create([
            'link_id' => $link->id,
            'action' => 'Redirect',
            'created_at' => Carbon::now(),
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Log a new Link created
     *
     * @param Link $link
     */
    public static function new(Link $link)
    {
        $currentUserId = Auth::user()->getAuthIdentifier();

        Activity::create([
            'user_id' => $currentUserId ?? null,
            'link_id' => $link->id,
            'action' => 'Create',
            'created_at' => Carbon::now(),
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Log an Error
     *
     * @param Link|null $link
     * @param string $details
     */
    public static function error(Link $link = null, string $details = '')
    {
        $currentUserId = Auth::user()->getAuthIdentifier();

        Activity::create([
            'user_id' => $currentUserId ?? null,
            'link_id' => $link ? $link->id : null,
            'action' => 'Error',
            'details' => $details,
            'created_at' => Carbon::now(),
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Return selected fields only
     * @return array
     */
    public function toArray(): array
    {
        return [
            'action' => $this->action,
            'short' => $this->link->short,
            'long' => $this->link->long,
            'created' => $this->created_at,
            'ip_address' => $this->ip_address
        ];
    }

}
