<?php

namespace Gitonomy\Browser\Routing;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Gitonomy\Git\Commit;
use Gitonomy\Git\Repository;
use Gitonomy\Git\Reference;

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
        return $this->generator->generate('commit', array('hash' => $commit->getHash(), 'repositoryName' => $this->getName($commit->getRepository())));
    }

    public function generateReferenceUrl(Reference $reference)
    {
        return $this->generator->generate('reference', array('fullname' => $reference->getFullname(), 'repositoryName' => $this->getName($reference->getRepository())));
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
