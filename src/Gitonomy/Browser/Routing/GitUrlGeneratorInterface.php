<?php

namespace Gitonomy\Browser\Routing;

use Gitonomy\Git\Commit;
use Gitonomy\Git\Reference;

interface GitUrlGeneratorInterface
{
    public function generateCommitUrl(Commit $commit);
    public function generateReferenceUrl(Reference $reference);
}
