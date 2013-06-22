<?php

namespace Gitonomy\Browser\Routing;

use Gitonomy\Browser\Git\Repository;
use Gitonomy\Bundle\GitBundle\Routing\GitUrlGeneratorInterface;
use Gitonomy\Git\Commit;
use Gitonomy\Git\Reference;
use Gitonomy\Git\Revision;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GitUrlGenerator implements GitUrlGeneratorInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    protected $generator;

    /**
     * List of repositories, used to get name from object.
     *
     * @var array
     */
    protected $repositories;

    public function __construct(UrlGeneratorInterface $generator, array $repositories)
    {
        $this->generator = $generator;
        $this->repositories = $repositories;
    }

    public function generateCommitUrl(Commit $commit)
    {
        return $this->generator->generate('commit', array('hash' => $commit->getHash(), 'repository' => $this->getName($commit->getRepository())));
    }

    public function generateReferenceUrl(Reference $reference)
    {
        return $this->generator->generate('reference', array('fullname' => $reference->getFullname(), 'repository' => $this->getName($reference->getRepository())));
    }

    public function generateTreeUrl(Revision $revision, $path = '')
    {
        return $this->generator->generate('tree', array('revision' => $revision->getRevision(), 'path' => $path, 'repository' => $this->getName($revision->getRepository())));
    }


    private function getName(Repository $repository)
    {
        $res = array_search($repository, $this->repositories);

        if (null === $res || false === $res) {
            throw new \RuntimeException(sprintf("Unaware of a repository located in %s\nKnown are: %s", $repository->getGitDir(), implode(', ', array_keys($this->repositories))));
        }

        return $res;
    }
}
