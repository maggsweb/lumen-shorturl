<?php

class ApiTest extends TestCase
{
    /**
     * @group Api
     */
    public function testMissingHeaderToken()
    {
        $this->post('/create');

        $this->seeStatusCode(401);
        $this->assertStringContainsString('Unauthorized', $this->response->getContent());
    }

    /**
     * @group Api
     */
    public function testInvalidHeaderToken()
    {
        $this->post('/create', [], [
            'HTTP_Authorization' => 'Basic INVALID00000HEADER',
        ]);

        $this->seeStatusCode(401);
        $this->assertStringContainsString('Unauthorized', $this->response->getContent());
    }

    /**
     * @group Api
     */
    public function testMissingBody()
    {
        $this->post('/create', [], [
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString,
        ]);

        $this->seeStatusCode(422);
        $this->assertJson($this->response->getContent());
        $this->assertStringContainsString('The long url field is required.', $this->response->getContent());
    }

    /**
     * @group Api
     */
    public function testInvalidBody()
    {
        $this->post('/create', [
            'long_url' => 'some-invalid-url',
        ], [
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString,
        ]);

        $this->seeStatusCode(422);
        $this->assertJson($this->response->getContent());
        $this->assertStringContainsString('The long url format is invalid.', $this->response->getContent());
    }

    /**
     * @group Api
     */
    public function testValidRequest()
    {
        $this->post('/create', [
            'long_url'  => 'http://www.apitest.com/aa/bb/cc/dd/ee/ff/gg',
            'short_url' => 'alpha',
        ], [
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString,
        ]);

        $this->seeStatusCode(201);
        $this->assertJson($this->response->getContent());
        $this->assertStringContainsString('www.apitest.com', $this->response->getContent());
    }

    /**
     * @group Api
     */
    public function testCreateEndpointIsRateLimited()
    {
        $header = ['HTTP_Authorization' => 'Basic '.$this->user->basicAuthString];
        $data = ['long_url' => 'http://www.rate-limit-test.com'];

        // 30 requests/min are allowed for the authenticated user
        for ($i = 0; $i < 30; $i++) {
            $this->post('/create', $data, $header);
            $this->assertNotEquals(429, $this->response->getStatusCode());
        }

        // The 31st request exceeds the limit
        $this->post('/create', $data, $header);

        $this->seeStatusCode(429);
        $this->seeJson(['error' => 'Too many requests.']);
    }
}
