<?php

namespace Gitonomy\Browser\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\Routing\RequestContext;

class RepositoryListener implements EventSubscriberInterface
{
    private $requestContext;
    private $twig;
    private $repositories;

    public function __construct(RequestContext $requestContext, \Twig_Environment $twig, array $repositories)
    {
        $this->requestContext = $requestContext;
        $this->twig           = $twig;
        $this->repositories   = $repositories;
    }

    public function onKernelController(KernelEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->attributes->has('repository')) {
            return;
        }

        $repository = $request->attributes->get('repository');

        if (!isset($this->repositories[$repository])) {
            throw new HttpException(404, sprintf('Repository "%s" does not exist', $repository));
        }

        $this->requestContext->setParameter('repository', $repository);

        $repositoryObject = $this->repositories[$repository];

        $request->attributes->set('repository', $repositoryObject);

        $this->twig->addGlobal('repository', $repositoryObject);
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );
    }
}
