<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Lumen\Http\Redirector;

class RedirectController extends Controller
{
    /**
     * Redirect ShortCode to LongURL
     *
     * @param Request $request
     * @param string $link
     *
     * @return JsonResponse|Redirector|RedirectResponse
     */
    public function redirect(Request $request, string $link)
    {
        if ($link = Link::retrieve($link)) {
            Activity::redirect($link);
            return redirect($link->getLongUrl(), 302);
        }
        return response()->json(['Link not found'], 500);
    }
}
