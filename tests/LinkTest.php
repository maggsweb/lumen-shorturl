<?php

class LinkTest extends TestCase
{
    /**
     * @group Links
     */
    public function testCreateLink()
    {
        $this->post('/create', [
            'long_url' => 'http://www.my-valid-url.com',
        ], [
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString,
        ]);

        $this->seeStatusCode(201);
        $this->assertStringContainsString('www.my-valid-url.com', $this->response->getContent());
        $this->assertJson($this->response->getContent());
    }

    /**
     * @group Links
     */
    public function testReturnsExistingLink()
    {
        $url = 'http://www.my-valid-url.com';

        $data = [
            'long_url' => $url,
        ];

        $header = [
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString,
        ];

        $this->post('/create', $data, $header);  // 201 New Link
        $this->post('/create', $data, $header);  // 200 Existing Link

        $this->seeStatusCode(200);
        $this->assertStringContainsString(json_encode($url), $this->response->getContent());
        $this->assertJson($this->response->getContent());
    }

    /**
     * @group Links
     */
    public function testUseSuggestedShortCode()
    {
        $long_url = 'http://www.my-second-valid-url.com';
        $short_url = 'shortlink';

        $data = [
            'long_url'  => $long_url,
            'short_url' => $short_url,
        ];

        $header = [
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString,
        ];

        $this->post('/create', $data, $header);

        $this->seeStatusCode(201);
        $this->assertStringContainsString(json_encode($long_url), $this->response->getContent());
        $this->assertStringContainsString($short_url, $this->response->getContent());
        $this->assertJson($this->response->getContent());
    }
}
