<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\RedirectResponse;
use Laravel\Lumen\Http\Redirector;
use Laravel\Lumen\Http\Request;

class LinkController extends Controller
{
    /**
     * @var Authenticatable|null
     */
    protected $user;

    /**
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = Auth()->user();
    }

    /**
     * Return existing/new short link
     *
     * @param Request $request
     * @return string|null
     * @throws Exception
     */
    public function create(Request $request)
    {

        $long_url = $request->json('long_url');

        // If the URL already exists for this user, return the same record
        $existingLing = Link::where('long', $long_url)->where('user_id', $this->user->id)->first();
        if ($existingLing) {
            return $existingLing;
        }

        // Otherwise, create a new record
        $short = $this->createShort();

        try {

            return Link::create([
                'short'     => $short,
                'long'      => $long_url,
                'user_id'   => $this->user->id
            ]);

        } catch (Exception $e) {
            //dump($e->getMessage());
            throw new Exception('Error creating new Link');
        }
    }

    /**
     * Generate a base32 random unique string, or retry
     *
     * @return string
     */
    private function createShort()
    {
        $short = base_convert(rand(), 10, 32);
        if (Link::where('short', $short)->exists()) {
            return $this->createShort();
        }
        return $short;
    }


    /**
     * @param Request $request
     * @param $link
     * @return RedirectResponse|Redirector
     */
    public function redirect(Request $request, $link)
    {
        $link = Link::where('short', $link)->first();
        if ($link) {
            return redirect($link->long);
        }
    }

}
