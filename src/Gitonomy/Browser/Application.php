<?php

namespace Gitonomy\Browser;

use Silex\Application as BaseApplication;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;

use Gitonomy\Browser\Controller\MainController;
use Gitonomy\Browser\EventListener\RepositoryListener;
use Gitonomy\Browser\Git\Repository;
use Gitonomy\Browser\Routing\GitUrlGenerator;
use Gitonomy\Browser\Twig\GitExtension;
use Gitonomy\Browser\Utils\RepositoriesFinder;

class Application extends BaseApplication
{
    /**
     * Constructor.
     *
     * @param string $configFile The config file to load.
     * @param array  $extraParam An array to overide params in configFile (usefull for test)
     */
    public function __construct($configFile, array $extraParam = array())
    {
        parent::__construct($extraParam);

        $gitonomy = $this;

        if (!file_exists($configFile)) {
            throw new \RuntimeException(sprintf('Can not find config file: "%s"', $configFile));
        }
        require $configFile;

        $this->loadRepositories();

        // Silex Service Provider
        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new TranslationServiceProvider(), array('locale_fallback' => 'en'));
        $this->register(new FormServiceProvider());
        $this->register(new ServiceControllerServiceProvider());
        $this->register(new TwigServiceProvider(), array(
            'twig.path' => __DIR__.'/Resources/views',
            'debug'     => $this['debug'],
        ));

        if ($this['debug']) {
            $this->register($profiler = new WebProfilerServiceProvider(), array(
                'profiler.cache_dir' => __DIR__.'/../../../cache/profiler',
            ));
            $this->mount('/_profiler', $profiler);
        }

        // Gitonomy\Browser Service Provider
        $urlGenerator = new GitUrlGenerator($this['url_generator'], $this['repositories']);
        $this['twig']->addExtension(new GitExtension($urlGenerator, array('git/default_theme.html.twig')));

        // Register the Repository Listener
        $this['dispatcher']->addSubscriber(new RepositoryListener($this['request_context'], $this['twig'], $this['repositories']));

        // Declaring controller
        $this['controller.main'] = $this->share(function() use ($gitonomy) {
            return new MainController($gitonomy['twig'], $gitonomy['url_generator'], $gitonomy['repositories']);
        });

        $this->registerRouting();
    }

    public function registerRouting()
    {
        /** Main page, showing all repositories. */
        $this
            ->get('/', 'controller.main:listAction')
            ->bind('repositories')
        ;

        /** Landing page of a repository. */
        $this
            ->get('/{repository}', 'controller.main:showRepositoryAction')
            ->bind('repository')
        ;

        /** Ajax Log entries */
        $this
            ->get('/{repository}/log-ajax', 'controller.main:logAjaxAction')
            ->bind('log_ajax')
        ;

        /** Commit page */
        $this
            ->get('/{repository}/commit/{hash}', 'controller.main:showCommitAction')
            ->bind('commit')
        ;

        /** Reference page */
        $this
            ->get('/{repository}/{fullname}', 'controller.main:showReferenceAction')
            ->bind('reference')
            ->assert('fullname', 'refs\\/.*')
        ;

        /** Delete a reference */
        $this
            ->post('/{repository}/admin/delete-ref/{fullname}', 'controller.main:deleteReferenceAction')
            ->bind('reference_delete')
            ->assert('fullname', 'refs\\/.*')
        ;
    }

    private function loadRepositories()
    {
        if (!isset($this['repositories'])) {
            throw new \RuntimeException(sprintf('You should declare some repositories in the config file: "%s"', $configFile));
        } elseif (is_string($this['repositories'])) {
            $repoFinder = new RepositoriesFinder();
            $this['repositories'] = $repoFinder->getRepositories($this['repositories']);
        } elseif ($this['repositories'] instanceof Repository) {
            $this['repositories'] = array($this['repositories']);
        } elseif (is_array($this['repositories'])) {
            foreach ($this['repositories'] as $key => $repository) {
                if (!$repository instanceof Repository) {
                    throw new \RuntimeException(sprintf('Value (%s) in $gitonomy[\'repositories\'] is not an instance of Repository in: "%s"', $key, $configFile));
                }
                if (is_string($key)) {
                    $repository->setName($key);
                }
            }
        } else {
            throw new \RuntimeException(sprintf('"$gitonomy" should be a array of Repository or a string in "%s"', $configFile));
        }

        $repositoryTmp = array();
        foreach ($this['repositories'] as $repository) {
            $repositoryTmp[$repository->getName()] = $repository;
        }

        $this['repositories'] = $repositoryTmp;
    }
}
