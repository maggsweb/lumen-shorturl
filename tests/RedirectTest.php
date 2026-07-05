<?php

class RedirectTest extends TestCase
{
    /**
     * @group Redirect
     */
    public function testLinkRedirects()
    {
        $link = $this->links->random();

        $this->get('/'.$link->short);

        $this->seeStatusCode(302);
    }

    /**
     * @group Redirect
     */
    public function testUnknownLinkReturns404()
    {
        $this->get('/this-code-does-not-exist');

        $this->seeStatusCode(404);
        $this->seeJson(['error' => 'Link not found']);
    }
}
