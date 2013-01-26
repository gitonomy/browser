<?php

namespace Gitonomy\Browser\Controller;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

    public function showRepositoryAction()
    {
        return $this->twig->render('log.html.twig');
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
