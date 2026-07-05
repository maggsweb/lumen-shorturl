<?php

use App\Models\Link;

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

    /**
     * @group User
     */
    public function testCanFilterActivityByOwnLink()
    {
        $link = $this->links->first();

        $this->json('GET', '/activity', [
            'short_url' => $link->short,
        ], [
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString,
        ]);

        $this->seeStatusCode(200);
        $this->assertJson($this->response->getContent());
    }

    /**
     * A user must not be able to read activity for another user's link.
     *
     * @group User
     */
    public function testCannotFilterActivityByAnotherUsersLink()
    {
        $foreignLink = Link::factory()->create([
            'user_id' => $this->alt_user->id,
        ]);

        $this->json('GET', '/activity', [
            'short_url' => $foreignLink->short,
        ], [
            'HTTP_Authorization' => 'Basic '.$this->user->basicAuthString,
        ]);

        $this->seeStatusCode(404);
    }
}
