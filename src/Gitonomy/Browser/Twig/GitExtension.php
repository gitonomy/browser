<?php

namespace Gitonomy\Browser\Twig;

use Gitonomy\Git\Commit;
use Gitonomy\Git\Log;

class GitExtension extends \Twig_Extension
{
    private $urlGenerator;
    private $themes;

    public function __construct(GitUrlGeneratorInterface $urlGenerator, array $themes = array())
    {
        $this->urlGenerator = $urlGenerator;
        $this->themes       = $themes;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('git_author',            array($this, 'renderAuthor'),           array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_commit_header',     array($this, 'renderCommitHeader'),     array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_log',               array($this, 'renderLog'),              array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_log_rows',          array($this, 'renderLogRows'),          array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_render',            array($this, 'renderBlock'),            array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_url',               array($this, 'getUrl')),
        );
    }

    public function getUrl($value)
    {
        if ($value instanceof Commit) {
            return $this->urlGenerator->generateCommitUrl($value);
        } else {
            throw new \InvalidArgumentException(sprintf('Unsupported type for URL generation: %s. Expected a Commit', is_object($value) ? get_class($value) : gettype($value)));
        }
    }

    public function renderCommitHeader(\Twig_Environment $env, Commit $commit)
    {
        return $this->renderBlock($env, 'commit_header', array(
            'commit' => $commit,
        ));
    }

    public function renderLog(\Twig_Environment $env, Log $log, array $options = array())
    {
        $options = array_merge(array(
            'query_url' => null,
            'per_page'  => 20
        ), $options);

        return $this->renderBlock($env, 'log', array(
            'log'      => $log,
            'query_url' => $options['query_url'],
            'per_page' => $options['per_page']
        ));
    }

    public function renderLogRows(\Twig_Environment $env, Log $log, array $options = array())
    {
        return $this->renderBlock($env, 'log_rows', array(
            'log'      => $log
        ));
    }

    public function renderAuthor(\Twig_Environment $env, Commit $commit, array $options = array())
    {
        $options = array_merge(array(
            'size' => 15
        ), $options);

        return $this->renderBlock($env, 'author', array(
            'name'      => $commit->getAuthorName(),
            'size'      => $options['size'],
            'email'     => $commit->getAuthorEmail(),
            'email_md5' => md5($commit->getAuthorEmail())
        ));
    }

    public function getName()
    {
        return 'git';
    }

    public function renderBlock(\Twig_Environment $env, $block, $context = array())
    {
        foreach ($this->themes as $theme) {
            $tpl = $env->loadTemplate($theme);
            if ($tpl->hasBlock($block)) {
                return $tpl->renderBlock($block, $context);
            }
        }

        throw new \InvalidArgumentException('Unable to find block '.$block);
    }
}
