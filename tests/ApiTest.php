<?php

class ApiTest extends TestCase
{
    /**
     * @group Api
     */
    public function testMissingHeaderToken()
    {
        $this
            ->post('/create')
            ->seeStatusCode(401)
            ->assertStringContainsString(
                'Unauthorized',
                $this->response->getContent()
            );
    }

    /**
     * @group Api
     */
    public function testInvalidHeaderToken()
    {
        $this
            ->post('/create', [], [
                'HTTP_Authorization' => 'Basic INVALID00000HEADER',
            ])
            ->seeStatusCode(401)
            ->assertStringContainsString(
                'Unauthorized',
                $this->response->getContent()
            );
    }

    /**
     * @group Api
     */
    public function testMissingBody()
    {
        $this
            ->post('/create', [], [
                'HTTP_Authorization' => 'Basic '.base64_encode("{$this->user->email}:password"),
            ])
            ->seeStatusCode(422)
            ->assertJson('{"long_url":["The long url field is required."]}');
    }

    /**
     * @group Api
     */
    public function testInvalidBody()
    {
        $this
            ->post('/create', [
                'long_url' => 'some-invalid-url',
            ], [
                'HTTP_Authorization' => 'Basic '.base64_encode("{$this->user->email}:password"),
            ])
            ->seeStatusCode(422)
            ->assertJson('{"long_url":["The long url format is invalid."]}');
    }

    /**
     * @group Api
     */
    public function testValidHeaderToken()
    {
        $this
            ->post('/create', [
	            'long_url' => 'http://www.apitest.com/aa/bb/cc/dd/ee/ff/gg',
                'short_url' => 'alpha'
            ], [
                'HTTP_Authorization' => 'Basic '.base64_encode("{$this->user->email}:password"),
            ])
            ->seeStatusCode(201);
    }

}
