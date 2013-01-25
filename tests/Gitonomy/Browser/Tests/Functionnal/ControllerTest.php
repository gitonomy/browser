<?php

use Silex\WebTestCase;

class ApplicationTest extends WebTestCase
{
    public function createApplication()
    {
        $rootDir = __DIR__.'/../../../../..';

        return new Gitonomy\Browser\Application(
            $rootDir.'/config/test.php',
            array('repositories' => array('browser' => new \Gitonomy\Browser\Git\Repository($rootDir)))
        );
    }

    public function test404()
    {
        $client = $this->createClient();

        $client->request('GET', '/give-me-a-404');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    public function getPage200Tests()
    {
        return array(
            array('/'),
            array('/browser'),
            array('/browser'),
            array('/browser/refs/heads/master'),
            array('/browser/commit/3c05a60d9522eb438d7be74f4ae51b4bcd0f697f'),
        );
    }

    /**
     * @dataProvider getPage200Tests
     */
    public function testPage200($url)
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', $url);

        $this->assertTrue($client->getResponse()->isOk());
    }
}
