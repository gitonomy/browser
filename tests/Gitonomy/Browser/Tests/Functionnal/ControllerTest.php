<?php

use Silex\WebTestCase;

use Gitonomy\Browser\Git\Repository;
use Gitonomy\Git\Admin;

class ApplicationTest extends WebTestCase
{
    const TEST_REPOSITORY = 'git@github.com:gitonomy/foobar.git';

    public static function createRepository()
    {
        $tmp = sys_get_temp_dir().'gitonomybrowser_foobar';
        if (!is_dir($tmp)) {
            Admin::cloneTo($tmp, self::TEST_REPOSITORY);
        }

        return new Repository($tmp);
    }

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
            array('/browser/tree/master/path'),
            array('/browser/tree/3c05a60d9522eb438d7be74f4ae51b4bcd0f697f/path/composer.json'),
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
