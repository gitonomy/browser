<?php

namespace Gitonomy\Browser\Twig;

use Gitonomy\Git\Commit;

interface GitUrlGeneratorInterface
{
    public function generateCommitUrl(Commit $commit);
}
