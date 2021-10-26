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
        $this->assertStringContainsString('Unauthorized',$this->response->getContent());
    }

    /**
     * @group Api
     */
    public function testInvalidHeaderToken()
    {
        $this->post('/create', [], [
            'HTTP_Authorization' => 'Basic INVALID00000HEADER'
        ]);

        $this->seeStatusCode(401);
        $this->assertStringContainsString('Unauthorized',$this->response->getContent());
    }

    /**
     * @group Api
     */
    public function testMissingBody()
    {
        $this->post('/create', [], [
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString
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
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString
        ]);

        $this->seeStatusCode(422);
        $this->assertJson($this->response->getContent());
        $this->assertStringContainsString('The long url format is invalid.',$this->response->getContent());
    }

    /**
     * @group Api
     */
    public function testValidRequest()
    {
        $this->post('/create', [
            'long_url' => 'http://www.apitest.com/aa/bb/cc/dd/ee/ff/gg',
            'short_url' => 'alpha'
        ], [
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString,
        ]);

        $this->seeStatusCode(201);
        $this->assertJson($this->response->getContent());
        $this->assertStringContainsString('www.apitest.com',$this->response->getContent());
    }

}
