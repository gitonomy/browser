<?php

namespace Gitonomy\Browser\Git;

use Gitonomy\Git\Repository as BaseRepository;
use Psr\Log\LoggerInterface;

class Repository extends BaseRepository
{
    private $name;

    public function __construct($path, array $options = array())
    {
        parent::__construct($path, $options);

        $this->setName(basename($this->getPath()));
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
