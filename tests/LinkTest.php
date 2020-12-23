<?php

class LinkTest extends TestCase
{
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
