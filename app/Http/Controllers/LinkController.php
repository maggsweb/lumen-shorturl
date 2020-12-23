<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Link;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\Redirector;
use Laravel\Lumen\Http\ResponseFactory;

class LinkController extends Controller
{
    /**
     * Return existing/new Link.
     *
     * @param Request $request
     *
     * @throws Exception
     *
     * @return Response|ResponseFactory
     */
    public function createLink(Request $request)
    {
        // Merge JSON body with request to Validate
        $data = $request->merge((array) json_decode($request->getContent()));

        $this->validate($data, [
            'long_url'  => ['required', 'url', 'max:255'],
            'short_url' => ['sometimes', 'min:5', 'max:20', 'alpha'],
        ]);

        $long_url = $request->json('long_url');
        $suggested_short_url = $request->json('short_url');

        $currentUserId = Auth::user()->getAuthIdentifier();

        // If the URL already exists for this user, return the same record
        $existingLink = Link::where('long', $long_url)->where('user_id', $currentUserId)->first();
        if ($existingLink) {
            return response($existingLink, 200);
        }

        // Otherwise, create a new record
        $short = $this->createShortCode($suggested_short_url);

        try {
            $newLink = Link::create([
                'short'     => $short,
                'long'      => $long_url,
                'user_id'   => $currentUserId,
            ]);

            Activity::new($newLink);

            return response($newLink, 201);
        } catch (Exception $e) {
            Activity::error(null, $e->getMessage());

            return response('Error creating new Link', 500);
        }
    }

    /**
     * Delete a Link and associated Activity.
     *
     * @param Request $request
     *
     * @throws ValidationException
     *
     * @return Response|ResponseFactory
     */
    public function deleteLink(Request $request)
    {
        // Merge JSON body with request to Validate
        $request->merge((array) json_decode($request->getContent()));

        $this->validate($request, [
            'short_url' => ['required', 'exists:links,short'],
        ]);

        $short_url = $request->json('short_url');

        $link = Link::byShortUrl($short_url)->first();
        if (!$link) {
            return response('Link not found', 500);
        }

        try {
            DB::beginTransaction();
            $link->activity()->delete();
            $link->delete();
            DB::commit();

            return response('Link deleted');
        } catch (Exception $e) {
            DB::rollBack();
            Activity::error(null, $e->getMessage());

            return response('Error deleting Link', 500);
        }
    }

    /**
     * Redirect to an existing Link.
     *
     * @param Request $request
     * @param $link
     *
     * @return RedirectResponse|Response|Redirector|ResponseFactory
     */
    public function redirect(Request $request, $link)
    {
        $link = Link::where('short', $link)->first();
        if ($link) {
            Activity::redirect($link);

            return redirect($link->long);
        }

        return response('Link not found', 500);
    }

    /**
     * Generate a base32 random unique string, or retry.
     *
     * @param null $suggested
     *
     * @return string
     */
    private function createShortCode($suggested = null): string
    {
        if ($suggested && !Link::where('short', $suggested)->exists()) {
            return $suggested;
        }

        $short = base_convert(rand(), 10, 32);

        if (Link::where('short', $short)->exists()) {
            return $this->createShortCode();
        }

        return $short;
    }
}
