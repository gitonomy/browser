<?php

namespace Gitonomy\Browser\Tests\Utils;

use Gitonomy\Browser\Utils\RepositoriesFinder;
use Gitonomy\Git\Admin;

class RepositoriesFinderTest extends \PHPUnit_Framework_TestCase
{

    public function testOk()
    {
        $finder = new RepositoriesFinder();

        $tmpDir = tempnam(sys_get_temp_dir(), 'gitlib_');
        unlink($tmpDir);
        mkdir($tmpDir.'/folder/subfolder', 0777, true);

        Admin::init($tmpDir.'/folder/subfolder/A', false);
        Admin::init($tmpDir.'/folder/B', false);
        Admin::init($tmpDir.'/C', false);

        $repositories = $finder->getRepositories($tmpDir);
        $this->assertCount(3, $repositories);
        $this->assertInstanceOf('Gitonomy\Git\Repository', reset($repositories));
    }
}
