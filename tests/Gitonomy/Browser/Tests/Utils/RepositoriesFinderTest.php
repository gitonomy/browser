<?php

namespace Gitonomy\Browser\Tests\Utils;

use Gitonomy\Browser\Utils\RepositoriesFinder;

class RepositoriesFinderTest extends \PHPUnit_Framework_TestCase
{

    public function testOk()
    {
        $finder = new RepositoriesFinder();

        $repositories = $finder->getRepositories(__DIR__.'/../../../../../vendor/twig');

        $this->assertCount(1, $repositories);
        $this->assertInstanceOf('Gitonomy\Git\Repository', reset($repositories));
    }
}
