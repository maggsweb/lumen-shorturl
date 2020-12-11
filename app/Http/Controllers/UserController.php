<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Http\Request;
use Laravel\Lumen\Http\ResponseFactory;

class UserController extends Controller
{

    /**
     * List activity for a User
     *  - optionally filter vy 'short_url'
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
            'short_url' => ['sometimes', 'exists:links,short']
        ]);

        $short_url = $request->json('short_url');
        if ($short_url) {

            // Get link
            $link = Link::byShortUrl($short_url)->first();
            $activity = Activity::forUser()->forLink($link->id);//->get();

        } else {

            $activity = Activity::forUser();//->get();

        }

        $activityLogs = $activity->count() > 15
            ? $activity->paginate(15)
            : $activity->get();

        return response()->json($activityLogs, 200);
    }



}
