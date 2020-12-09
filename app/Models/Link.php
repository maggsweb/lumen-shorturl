<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create($array)
 * @method static where($column, $value)
 */
class Link extends Model
{

    protected $guarded = [];

    /**
     * @var mixed
     */
    protected $domain;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->domain = env('APP_URL');
    }


//    public function getRouteKeyName()
//    {
//        return 'short';
//    }


    /**
     * Return selected fields only
     * @return array
     */
    public function toArray()
    {
        return [
            'short'   => $this->attributes['short'],
            'full'    => $this->domain . '/' . $this->attributes['short'],
            'long'    => $this->attributes['long'],
            'created' => $this->attributes['created_at']
        ];
    }

}
