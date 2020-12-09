<?php

namespace App\Http\Controllers;


use Laravel\Lumen\Http\Request;

class UrlController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function create(Request $request)
    {
        dump(
            __FUNCTION__,
//          Auth()->user(),
          Auth()->user()->token,
          Auth()->user()->name,
//            $request,
            $request->json(),                // Parameter Bag
            $request->json('name'),     // Specific json body value
            $request->getContent()           // Body json string
        );




    }


}
