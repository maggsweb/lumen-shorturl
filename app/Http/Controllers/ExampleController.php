<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Lumen\Application;

class ExampleController extends Controller
{
    /**
     * @return View|Application
     */
    public function test()
    {
        return view('test',[
            'long_url' => null,
            'create' => null
        ]);
    }

    /**
     * Example Create
     *
     * @param Request $request
     * @return View|Application
     * @throws GuzzleException
     */
    public function create(Request $request)
    {
        $api_url = url() . '/create';

        $api_key = 'f1b41b1f-7d94-4424-a387-c96ec3a65521';

        $request->merge((array)json_decode($request->getContent()));

        $data = json_encode([
            'long_url' => $request->get('long_url')
        ]);

        //-- Guzzle ------------------------------------------
        $client = new Client([
            'headers' => [ 'token' => $api_key ]
        ]);
        $response = $client->post($api_url,
            ['body' => $data]
        );
        $data = $response->getBody()->getContents();

        //-- cURL ------------------------------------------
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $api_url);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, ["token:$api_key"]);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        $data = curl_exec($ch);
//        curl_close($ch);

        //-----------------------------------------------

        return view('test', [
            'long_url' => $request->get('long_url'),
            'create' => json_encode(json_decode($data), JSON_PRETTY_PRINT)
        ]);

    }

}
