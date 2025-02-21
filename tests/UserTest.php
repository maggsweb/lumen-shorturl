<?php

class UserTest extends TestCase
{
    /**
     * @group User
     */
    public function testInvalidUser()
    {
        $this->get('/links', [
            'HTTP_Authorization' => 'Basic INVALID_USER_STRING',
        ]);

        $this->seeStatusCode(401);
        $this->assertStringContainsString(
            'Unauthorized',
            $this->response->getContent()
        );
    }

    /**
     * @group User
     */
    public function testValidUser()
    {
        $this->get('/links', [
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString,
        ]);

        $this->seeStatusCode(200);
        $this->assertJson($this->response->getContent());
    }

    /**
     * @group User
     */
    public function testUserActivity()
    {
        $this->get('/activity', [
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString,
        ]);

        $this->seeStatusCode(200);
        $this->assertJson($this->response->getContent());
        $this->assertSame(
            $this->activity->count(),
            count(json_decode($this->response->getContent()))
        );
    }
}
