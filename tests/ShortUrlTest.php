<?php

class ShortUrlTest extends TestCase
{
    public function testMissingHeaderToken()
    {
        $this->post('/create');

        $this->seeStatusCode(401);
        $this->assertStringContainsString(
            'Unauthorized',
            $this->response->getContent()
        );
    }

    public function testInvalidHeaderToken()
    {
        $header = [
            'HTTP_token' => '123456789',
        ];

        $this->post('/create', [], $header);

        $this->seeStatusCode(401);
        $this->assertStringContainsString(
            'Unauthorized',
            $this->response->getContent()
        );
    }

    public function testMissingBody()
    {
        $data = [];
        $header = [
            'HTTP_token' => $this->user->uuid,
        ];

        $this->post('/create', $data, $header);

        $this->seeStatusCode(422); // Validation
        $this->assertJson('{"long_url":["The long url field is required."]}');
    }

    public function testInvalidBody()
    {
        $data = [
            'long_url' => 'some-invalid-url',
        ];

        $header = [
            'HTTP_token' => $this->user->uuid,
        ];

        $this->post('/create', $data, $header);

        $this->seeStatusCode(422); // Validation
        $this->assertJson('{"long_url":["The long url format is invalid."]}');
    }

    public function testCreateLink()
    {
        $url = 'http://www.my-valid-url.com';

        $data = [
            'long_url' => $url,
        ];

        $header = [
            'HTTP_token' => $this->user->uuid,
        ];

        $this->json('POST', '/create', $data, $header);

        $this->seeStatusCode(201);
        $this->assertStringContainsString(
            json_encode($url),
            $this->response->getContent()
        );
//        $this->seeInDatabase('')
    }

    public function testReturnExistingLink()
    {
        $url = 'http://www.my-valid-url.com';

        $data = [
            'long_url' => $url,
        ];

        $header = [
            'HTTP_token' => $this->user->uuid,
        ];

        $this->json('POST', '/create', $data, $header);  // 201 New Link
        $this->json('POST', '/create', $data, $header);  // 200 Existing Link

        $this->seeStatusCode(200);
        $this->assertStringContainsString(
            json_encode($url),
            $this->response->getContent()
        );
    }

    public function testUseSuggestedShortCode()
    {
        $long_url = 'http://www.my-second-valid-url.com';
        $short_url = 'shortlink';

        $data = [
            'long_url'  => $long_url,
            'short_url' => $short_url,
        ];

        $header = [
            'HTTP_token' => $this->user->uuid,
        ];

        $this->json('POST', '/create', $data, $header);

        $this->seeStatusCode(201);
        $this->assertStringContainsString(
            json_encode($long_url),
            $this->response->getContent()
        );
        $this->assertStringContainsString(
            $short_url,
            $this->response->getContent()
        );
    }
}
