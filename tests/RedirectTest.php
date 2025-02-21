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
}
