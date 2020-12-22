<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JsonRequestMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])
            && $request->isJson()
        ) {
            $data = $request->json()->all();
            $request->request->replace(is_array($data) ? $data : []);
        }

        return $next($request);
    }
}

////
///
///
///
$app->middleware([
    App\Http\Middleware\JsonRequestMiddleware::class,
]);

//

https://bitpress.io/php/laravel/2016/02/16/how-to-accept-json-post-requests-in-lumen/
