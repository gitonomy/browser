<?php

namespace Gitonomy\Browser\Twig;

use Gitonomy\Git\Commit;
use Gitonomy\Git\Reference;

interface GitUrlGeneratorInterface
{
    public function generateCommitUrl(Commit $commit);
    public function generateReferenceUrl(Reference $reference);
}
