<?php

namespace Gitonomy\Browser\Utils;

use Gitonomy\Browser\Git\Repository;

class RepositoriesFinder
{
    public function getRepositories($path)
    {
        $repositories = array();

        if (false !== strpos($path, '*')) {
            $repositoriesTmp = $this->globDirectory($path);
        } else {
            $repositoriesTmp = $this->recurseDirectory($path);
        }

        foreach ($repositoriesTmp as $repo) {
            $repositories[] = new Repository($repo);
        }

        return $repositories;
    }

    private function globDirectory($path)
    {
        $repositories = array();

        foreach (glob($path, GLOB_ONLYDIR) as $dir) {
            $isBare = file_exists($dir . '/HEAD');
            $isRepository = file_exists($dir . '/.git/HEAD');

            if ($isRepository || $isBare) {
                $repositories[] = $dir;
            }
        }

        return $repositories;
    }

    /**
     * Ported from https://github.com/klaussilveira/gitter
     */
    private function recurseDirectory($path)
    {
        $dirs = new \DirectoryIterator($path);

        $repositories = array();

        foreach ($dirs as $dir) {
            if ($dir->isDot() || !$dir->isDir()) {
                continue;
            }

            // Ignore hidden directories
            if (0 === substr($dir->getFilename(), 0, 1)) {
                continue;
            }

            $isBare = file_exists($dir->getPathname() . '/HEAD');
            $isRepository = file_exists($dir->getPathname() . '/.git/HEAD');

            if ($isRepository || $isBare) {
                $repositories[] = $dir->getPathname();
                continue;
            }

            $repositories = array_merge($repositories, $this->recurseDirectory($dir->getPathname()));
        }

        return $repositories;
    }
}
