<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Link;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\Redirector;
use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Http\ResponseFactory;

class LinkController extends Controller
{
    /**
     * Return existing/new Link
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
            'long_url' => ['required','url', 'max:255'],
        ]);

        $long_url = $request->json('long_url');

        $currentUserId = Auth::user()->getAuthIdentifier();

        // If the URL already exists for this user, return the same record
        $existingLink = Link::where('long', $long_url)->where('user_id', $currentUserId)->first();
        if ($existingLink) {
            return response($existingLink, 200);
        }

        // @TODO detect possible short_url ?
        // Otherwise, create a new record
        $short = $this->createShortCode();

        try {

            $newLink = Link::create([
                'short'     => $short,
                'long'      => $long_url,
                'user_id'   => $currentUserId
            ]);

            Activity::new($newLink);

            return response($newLink, 201);

        } catch (Exception $e) {

            Activity::error(null, $e->getMessage());

            return response('Error creating new Link', 500);
        }
    }

    /**
     * Redirect to an existing Link
     *
     * @param Request $request
     * @param $link
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
     * List activity for a Link
     *
     * @param Request $request
     * @return JsonResponse|Response|ResponseFactory
     * @throws ValidationException
     */
    public function list(Request $request)
    {
        // Merge JSON body with request to Validate
        $request->merge((array)json_decode($request->getContent()));

        $this->validate($request, [
            'short_url' => ['required'],
        ]);

        $short_url = $request->json('short_url');

        $link = Link::byShortUrl($short_url)->first();
        if (!$link) {
            return response('Link not found', 500);
        }

        $activity = Activity::forLink($link->id);

        $activityLogs = $activity->count() > 15
            ? $activity->paginate(15)
            : $activity->get();

        return response()->json($activityLogs, 200);
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

}
