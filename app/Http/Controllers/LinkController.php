<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Link;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LinkController extends Controller
{
    /**
     * Return existing/new Link.
     *
     * @param Request $request
     *
     * @throws Exception
     *
     * @return JsonResponse
     */
    public function createLink(Request $request): JsonResponse
    {
        $this->validate($request, [
            'long_url'  => ['required', 'url', 'max:255'],
            'short_url' => ['sometimes', 'min:5', 'max:20', 'alpha'],
        ]);

        $long_url = $request->get('long_url');
        $suggested_short_url = $request->get('short_url');

        $currentUserId = Auth::user()->getAuthIdentifier();

        // If the URL already exists for this user, return the same record
        if ($existingLink = Link::withTrashed()->byLongUrl($long_url)->byUser($currentUserId)->first()) {
            if ($existingLink->trashed()) {
                $existingLink->restore();
            }

            return response()->json([$existingLink], 200);
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

            return response()->json([$newLink], 201);
        } catch (Exception $e) {
            Activity::error(null, $e->getMessage());

            return response()->json(['Error creating new Link'], 500);
        }
    }

    /**
     * Delete a Link and associated Activity.
     *
     * @param Request $request
     *
     * @throws ValidationException
     *
     * @return JsonResponse
     */
    public function deleteLink(Request $request): JsonResponse
    {
        $this->validate($request, [
            'short_url' => ['required', 'exists:links,short'],
        ]);

        $short_url = $request->json('short_url');

        $link = Link::byShortUrl($short_url)->first();
        if (!$link) {
            return response()->json(['Link not found'], 500);
        }

        try {
            DB::beginTransaction();
            $link->activity()->delete();
            $link->delete();
            DB::commit();

            return response()->json(['Link deleted']);
        } catch (Exception $e) {
            DB::rollBack();

            Activity::error(null, $e->getMessage());

            return response()->json(['Error deleting Link'], 500);
        }
    }

    /**
     * Generate a base32 random unique string, or retry.
     *
     * @param null $suggested
     *
     * @return string
     */
    protected function createShortCode($suggested = null): string
    {
        if ($suggested && !$this->shortCodeExists($suggested)) {
            return $suggested;
        }

        $short = ($suggested ? $suggested.'_' : '').base_convert(rand(), 10, 32);

        if ($this->shortCodeExists($short)) {
            return $this->createShortCode();
        }

        return $short;
    }

    /**
     * Check whether a short code exists in the db.
     *
     * @param $shortCode
     *
     * @return mixed
     */
    protected function shortCodeExists($shortCode)
    {
        return Link::byShortUrl($shortCode)->exists();
    }
}
