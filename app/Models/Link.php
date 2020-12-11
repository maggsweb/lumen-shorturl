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

//    public function getRouteKeyName()
//    {
//        return 'short';
//    }

    /**
     * The current FQ domain string
     * @return string
     */
    private function getDomain()
    {
        return env('APP_URL', '');
    }

    /**
     * Return selected fields only
     * @return array
     */
    public function toArray()
    {
        return [
            'short'   => $this->attributes['short'],
            'full'    => $this->getDomain() . '/' . $this->attributes['short'],
            'long'    => $this->attributes['long'],
            'created' => $this->attributes['created_at']
        ];
    }

}
