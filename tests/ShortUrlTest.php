<?php

use App\Models\User;

class ShortUrlTest extends TestCase
{

    //use \Laravel\Lumen\Testing\Concerns\MakesHttpRequests;

    /**
     *
     */
//    public function testMissingHeaderToken()
//    {
//        $this->post('/create');
//
//        $this->seeStatusCode(401);
//
//        $this->assertStringContainsString(
//            'Unauthorized',
//            $this->response->getContent()
//        );
//    }

    /**
     *
     * @TODO change this so an invalid header returns different to a missing header
     */
//    public function testInvalidHeaderToken()
//    {
//        $header = [
//            'HTTP_token' => '123456789'
//        ];
//
//        $this->post('/create', [], $header);
//
//        $this->seeStatusCode(401);
//
//        $this->assertStringContainsString(
//            'Unauthorized',
//            $this->response->getContent()
//        );
//    }

    /**
     * @TODO this isn't actually reading $data
     */
//    public function testMissingBody()
//    {
//        $data = [
//           // "long_url"=> "http://www.google.com"
//        ];
//        $header = [
//            'HTTP_token' => $this->user->uuid
//        ];
//
//        $this->post('/create', $data, $header);
//
////        dump(
////            $this->response->getStatusCode(),
////            $this->response->getContent()
////        );
//
//        $this->seeStatusCode(422); // Validation
//
//        $this->assertStringContainsString(
//            'The long url field is required.',
//            $this->response->getContent()
//        );
//
////        $this->assertJson('{"long_url":["The long url field is required."]}');
//    }


    /**
     *
     */
//    public function testInvalidBody()
//    {
//        $data = [
//            'long_url' => 'some-invalid-url'
//        ];
//
//        $header = [
//            'HTTP_token' => $this->user->uuid,
//            'Content-Type' => 'application/json'
//        ];
//
//        $this->post('/create', $data, $header);
////        $this->json('POST', '/create', $data, $header);
////        $this->call('POST', '/create', $data, $header);
//
//
//        dump(
//            $this->response->getStatusCode(),
//            $this->response->getContent()
//
//        );
//
////        $this->seeStatusCode(422); // Validation
////        $this->assertJson('{"long_url":["The long url field is required."]}');
//
//
//    }





    public function testCreateLink()
    {
        $data = [
//            'json' => [
                'long_url' => 'http://www.google.com'
//            ]
        ];
//        dump($data);
//        dump(json_encode($data));
//        exit;

//        $string = '{"long_url": "http://www.google.com"}';
//        $data = json_decode($string, true);
//        dump($data);

        $header = [
//            'HTTP_token' => $this->user->uuid
            'token' => $this->user->uuid
            //'Content-Type' => 'application/json'
        ];

//        $this->post('/create', $data, $header);
        $this->json('POST', '/create', $data, $header);

        dump(
            $this->response->getStatusCode(),
            $this->response->getContent()
        );

//        $this->seeStatusCode(200);
    }




//    public function test1()
//    {
//        $data = [
//            'long_url' => 'http://www.google.com'
//        ];
//        $content = json_encode($data);
//
//        $headers = [
//            'CONTENT_LENGTH' => mb_strlen($content, '8bit'),
//            'CONTENT_TYPE' => 'application/json',
//            'Accept' => 'application/json',
//            'token' => $this->user->uuid
//        ];
//
//        $this->call(
//            'POST', '/create', [], [], [], $this->transformHeadersToServerVars($headers), $content
//        );
//
//        dump(
//            $this->response->getStatusCode(),
//            $this->response->getContent()
//        );
//
//    }


}
