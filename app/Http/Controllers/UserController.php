<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * List the authenticated user's Links.
     *
     * @return JsonResponse
     */
    public function listLinks(): JsonResponse
    {
        $links = Link::byUser();
        $count = $links->count();

        if ($count === 0) {
            return response()->json('No Links found');
        }

        return response()->json(
            $count > 15 ? $links->paginate(15) : $links->get(),
            200
        );
    }

    /**
     * List activity for a User
     *  - optionally filter by 'short_url'.
     *
     * @param Request $request
     *
     * @throws ValidationException
     *
     * @return JsonResponse
     */
    public function listActivity(Request $request): JsonResponse
    {
        $this->validate($request, [
            'short_url' => ['sometimes', 'exists:links,short'],
        ]);

        // Activity by User
        $activity = Activity::forUser();

        $short_url = $request->json('short_url');
        if ($short_url) {
            // Scope to the authenticated user so activity for another user's link cannot be read
            $link = Link::byShortUrl($short_url)->byUser()->first();
            if (!$link) {
                return response()->json(['error' => 'Link not found'], 404);
            }
            $activity->forLink($link->id);
        }

        $activityLogs = $activity->count() > 15
            ? $activity->paginate(15)
            : $activity->get();

        return response()->json($activityLogs, 200);
    }

    /**
     * Delete a User, Links and Activity.
     *
     * @return JsonResponse
     */
    public function deleteUser(): JsonResponse
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

            return response()->json(['User deleted'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Activity::error(null, $e->getMessage());

            return response()->json(['error' => 'Error deleting User'], 500);
        }
    }
}
