<?php

namespace Gitonomy\Browser\Controller;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Gitonomy\Git\Blob;
use Gitonomy\Git\Tree;
use Gitonomy\Git\Exception\ReferenceNotFoundException;

class MainController
{
    private $twig;
    private $urlGenerator;
    private $repositories;

    public function __construct(\Twig_Environment $twig, UrlGenerator $urlGenerator, array $repositories = array())
    {
        $this->twig         = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->repositories = $repositories;
    }

    public function listAction()
    {
        return $this->twig->render('repository_list.html.twig', array('repositories' => $this->repositories));
    }

    public function repositoryAction()
    {
        return $this->twig->render('repository.html.twig');
    }

    public function logAction()
    {
        return $this->twig->render('log.html.twig');
    }

    public function statusAction()
    {
        return $this->twig->render('status.html.twig');
    }

    public function logAjaxAction(Request $request, $repository)
    {
        if ($reference = $request->query->get('reference')) {
            $log = $repository->getReferences()->get($reference)->getLog();
        } else {
            $log = $repository->getLog();
        }

        if (null !== ($offset = $request->query->get('offset'))) {
            $log->setOffset($offset);
        }

        if (null !== ($limit = $request->query->get('limit'))) {
            $log->setLimit($limit);
        }

        $log = $repository->getLog()->setOffset($offset)->setLimit($limit);

        return $this->twig->render('log_ajax.html.twig', array('log' => $log));
    }

    public function treeAction($repository, $revision, $path)
    {
        $revision = $repository->getRevision($revision);
        try {
            $commit = $revision->getCommit();
            $tree = $commit->getTree();
        } catch (ReferenceNotFoundException $e) {
            throw new NotFoundHttpException(sprintf('The revision "%s" is not valid', $revision), $e);
        }

        try {
            $element = $tree->resolvePath($path);
        } catch (\Exception $e) {
            throw new NotFoundHttpException(sprintf('Cannot find path "%s" for current commit "%s"', $path, $commit->getHash()), $e);
        }

        $template = $element instanceof Blob ? 'browse_blob.html.twig' : 'browse_tree.html.twig';

        return $this->twig->render($template, array(
            'element'  => $element,
            'path'     => $path,
            'revision' => $revision,
        ));
    }

    public function showCommitAction($repository, $hash)
    {
        return $this->twig->render('commit.html.twig', array(
            'commit' => $repository->getCommit($hash),
        ));
    }

    public function showReferenceAction($repository, $fullname)
    {
        return $this->twig->render('reference.html.twig', array(
            'reference' => $repository->getReferences()->get($fullname),
        ));
    }

    public function deleteReferenceAction()
    {
        $repository->getReferences()->get($fullname)->delete();

        return $this->redirect($this->urlGenerator->generate('repository'));
    }

    private function redirect($url, $status = 302)
    {
        return new RedirectResponse($url, $status);
    }
}
