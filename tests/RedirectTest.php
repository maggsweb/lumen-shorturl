<?php

class RedirectTest extends TestCase
{
    /**
     * Check random url redirects
     */
    public function testLinkRedirects()
    {
        $link = $this->links->random();

        $this->get('/'.$link->short);

        $this->seeStatusCode(302);
    }
}
