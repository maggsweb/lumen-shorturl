<?php

class HomepageTest extends TestCase
{
    /**
     * @group Homepage
     */
    public function testHomepage()
    {
        $this->get('/');
        $this->seeStatusCode(200);
        $this->assertStringContainsString(
            'URL Shortner API',
            $this->response->getContent()
        );
        $this->assertStringContainsString(
            'A back-end API in Lumen 8',
            $this->response->getContent()
        );
    }
}
