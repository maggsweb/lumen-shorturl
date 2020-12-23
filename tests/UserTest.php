<?php

use App\Models\Link;

class UserTest extends TestCase
{

    public function testUserIsInvalid()
    {
        $data = [];
        $header = [
            'HTTP_token' => 'invalid-user'
        ];

        $this->post('/user', $data, $header);

        $this->seeStatusCode(401);
        $this->assertStringContainsString(
            'Unauthorized',
            $this->response->getContent()
        );
    }

    public function testUserValid()
    {
        $data = [];
        $header = [
            'HTTP_token' => $this->user->uuid
        ];

        $this->post('/user', $data, $header);

        $this->seeStatusCode(200);
    }

    public function testUserHasLinks()
    {
        $data = [];
        $header = [
            'HTTP_token' => $this->user->uuid
        ];

        $this->post('/link', $data, $header);

        $data = json_decode($this->response->getContent(), 1);

        $this->seeStatusCode(200);
        $this->assertSame($this->links->count(), count($data));
    }

    public function testUserHasNoLinks()
    {
        $data = [];
        $header = [
            'HTTP_token' => $this->alt_user->uuid
        ];

        $this->post('/link', $data, $header);

        $this->seeStatusCode(200);
        $this->assertStringContainsString(
            'No Links found',
            $this->response->getContent()
        );

    }

}
