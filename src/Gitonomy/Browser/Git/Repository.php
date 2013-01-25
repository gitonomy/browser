<?php

namespace Gitonomy\Browser\Git;

use Gitonomy\Git\Repository as BaseRepository;
use Psr\Log\LoggerInterface;


class Repository extends BaseRepository
{
    private $name;

    public function __construct($dir, $workingDir = null, LoggerInterface $logger = null)
    {
        parent::__construct($dir, $workingDir, $logger);

        $this->setName(basename($this->getPath()));
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;

        return $this;
    }
}
