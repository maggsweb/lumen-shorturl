<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\Redirector;
use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Http\ResponseFactory;

class LinkController extends Controller
{
    /**
     * Return existing/new short link
     *
     * @param Request $request
     * @return Response|ResponseFactory
     * @throws Exception
     */
    public function create(Request $request)
    {
        // Merge JSON body with request to Validate
        $request->merge((array)json_decode($request->getContent()));

        $this->validate($request, [
            'long_url' => ['required','url'],
        ]);

        /** @var User $user */
        $user = Auth()->user();

        $long_url = $request->json('long_url');

        // If the URL already exists for this user, return the same record
        $existingLink = Link::where('long', $long_url)->where('user_id', $user->id)->first();
        if ($existingLink) {
            return response($existingLink, 200);
        }

        // Otherwise, create a new record
        $short = $this->createShortCode();

        try {

            $newLink = Link::create([
                'short'     => $short,
                'long'      => $long_url,
                'user_id'   => $user->id
            ]);

            return response($newLink, 201);

        } catch (Exception $e) {

            // dump($e->getMessage());

            return response('Error creating new Link', 500);
        }
    }

    /**
     * Generate a base32 random unique string, or retry
     *
     * @return string
     */
    private function createShortCode(): string
    {
        $short = base_convert(rand(), 10, 32);

        if (Link::where('short', $short)->exists()) {
            return $this->createShortCode();
        }
        return $short;
    }

    /**
     * Redirect to an existing URL, or abort
     *
     * @param Request $request
     * @param $link
     * @return RedirectResponse|Response|Redirector|ResponseFactory
     */
    public function redirect(Request $request, $link)
    {
        $link = Link::where('short', $link)->pluck('long')->first();
        if ($link) {

            // @TODO Log access

            return redirect($link);
        }
        return response('Link not found', 500);
    }

}
