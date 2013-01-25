<?php

namespace Gitonomy\Browser\Utils;

use Gitonomy\Git\Repository;

class RepositoriesFinder
{
    public function getRepositories($path)
    {
        $repositoriesTmp = $this->recurseDirectory($path);
        $repositories = array();

        foreach ($repositoriesTmp as $repo) {
            $repositories[]  = new Repository($repo);
        }

        return $repositories;
    }

    /**
     * Ported from https://github.com/klaussilveira/gitter
     */
    private function recurseDirectory($path)
    {
        $dir = new \DirectoryIterator($path);

        $repositories = array();

        foreach ($dir as $file) {
            if ($file->isDot()) {
                continue;
            }

            if (strrpos($file->getFilename(), '.') === 0) {
                continue;
            }

            if ($file->isDir()) {
                $isBare = file_exists($file->getPathname() . '/HEAD');
                $isRepository = file_exists($file->getPathname() . '/.git/HEAD');

                if ($isRepository || $isBare) {
                    $repositories[] = $file->getPathname();
                    continue;
                } else {
                    $repositories = array_merge($repositories, $this->recurseDirectory($file->getPathname()));
                }
            }
        }

        return $repositories;
    }
}
