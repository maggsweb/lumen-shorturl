<?php

class ApiTest extends TestCase
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
}
