<?php

namespace Gitonomy\Browser;

use Symfony\Component\HttpFoundation\Request;

use Silex\Application as BaseApplication;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

use Gitonomy\Browser\Twig\GitExtension;

class Application extends BaseApplication
{
    public function __construct(array $repositories)
    {
        parent::__construct();

        $this['repositories'] = $repositories;

        // urlgen
        $this->register(new UrlGeneratorServiceProvider());

        // form
        $this->register(new FormServiceProvider());

        // twig
        $this->register(new TwigServiceProvider(), array(
            'twig.path' => __DIR__.'/Resources/views'
        ));

        $urlGenerator = new GitUrlGenerator($this['url_generator'], $this['repositories']);

        $this['twig']->addExtension(new GitExtension($urlGenerator, array('git/default_theme.html.twig')));

        $this->registerActions();
    }

    public function registerActions()
    {
        /**
         * Main page, showing all repositories.
         */
        $this->get('/', function (Application $app) {
            return $app['twig']->render('repository_list.html.twig');
        })->bind('repositories');

        /**
         * Landing page of a repository.
         */
        $this->get('/{name}', function (Application $app, $name) {
            if (!isset($app['repositories'][$name])) {
                $app->abort(404, "Repository $name does not exist");
            }

            return $app['twig']->render('log.html.twig', array(
                'name'       => $name,
                'repository' => $app['repositories'][$name]
            ));
        })->bind('repository');

        /**
         * Ajax Log entries
         */
        $this->get('/{name}/log-ajax', function (Request $request, Application $app, $name) {
            if (!isset($app['repositories'][$name])) {
                $app->abort(404, "Repository $name does not exist");
            }

            $offset = $request->query->get('offset');
            $limit  = $request->query->get('limit');

            $log = $app['repositories'][$name]->getLog()->setOffset($offset)->setLimit($limit);

            return $app['twig']->render('log_ajax.html.twig', array(
                'name'       => $name,
                'log'        => $log
            ));
        })->bind('log_ajax');

        /**
         * Commit page
         */
        $this->get('/{name}/commit/{hash}', function (Application $app, $name, $hash) {
            if (!isset($app['repositories'][$name])) {
                $app->abort(404, "Repository $name does not exist");
            }

            return $app['twig']->render('commit.html.twig', array(
                'name'   => $name,
                'commit' => $app['repositories'][$name]->getCommit($hash),
            ));
        })->bind('commit');

    }
}
