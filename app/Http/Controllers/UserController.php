<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Http\ResponseFactory;

class UserController extends Controller
{
    /**
     * List activity for a Link.
     *
     * @param Request $request
     *
     * @throws ValidationException
     *
     * @return JsonResponse|Response|ResponseFactory
     */
    public function listLinks(Request $request)
    {
        // Merge JSON body with request to Validate
        $request->merge((array) json_decode($request->getContent()));

        $this->validate($request, [
            'short_url' => ['sometimes', 'exists:links,short'],
        ]);

        $links = Link::byUser();

        if (!$links->count()) {
            return response('No Links found', 200);
        }

        $userLinks = $links->count() > 15
            ? $links->paginate(15)
            : $links->get();

        return response()->json($userLinks, 200);
    }

    /**
     * List activity for a User
     *  - optionally filter by 'short_url'.
     *
     * @param Request $request
     *
     * @throws ValidationException
     *
     * @return JsonResponse|Response|ResponseFactory
     */
    public function listUser(Request $request)
    {
        // Merge JSON body with request to Validate
        $request->merge((array) json_decode($request->getContent()));

        $this->validate($request, [
            'short_url' => ['sometimes', 'exists:links,short'],
        ]);

        // Activity by User
        $activity = Activity::forUser();

        $short_url = $request->json('short_url');
        if ($short_url) {
            // Get link
            $link = Link::byShortUrl($short_url)->first();
            $activity->forLink($link->id);
        }

        $activityLogs = $activity->count() > 15
            ? $activity->paginate(15)
            : $activity->get();

        return response()->json($activityLogs, 200);
    }

    /**
     * Delete a User, Links and Activity.
     */
    public function deleteUser()
    {
        $authUser = Auth::user();

        try {
            DB::beginTransaction();
            // Delete User Links and Link Activity
            $authUser->links->each(function ($link) {
                $link->activity()->delete();
                $link->delete();
            });
            // Delete User Acvtivity
            $authUser->activity()->delete();
            // Delete User
            $authUser->delete();
            DB::commit();

            return response('User deleted');
        } catch (Exception $e) {
            DB::rollBack();
            Activity::error(null, $e->getMessage());

            return response('Error deleting User', 500);
        }
    }
}
