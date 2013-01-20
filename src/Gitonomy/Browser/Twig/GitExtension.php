<?php

namespace Gitonomy\Browser\Twig;

use Gitonomy\Git\Commit;
use Gitonomy\Git\Log;

class GitExtension extends \Twig_Extension
{
    private $themes;

    public function __construct(array $themes = array())
    {
        $this->themes = $themes;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('git_author',            array($this, 'renderAuthor'),           array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_render',            array($this, 'renderBlock'),            array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_commit_attributes', array($this, 'renderCommitAttributes'), array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_log',               array($this, 'renderLog'),              array('is_safe' => array('html'), 'needs_environment' => true)),
            new \Twig_SimpleFunction('git_log_rows',          array($this, 'renderLogRows'),          array('is_safe' => array('html'), 'needs_environment' => true)),
        );
    }

    public function renderLog(\Twig_Environment $env, $value, array $options = array())
    {
        $options = array_merge(array(
            'query_url' => null,
            'per_page'  => 20
        ), $options);

        if (!$value instanceof Log) {
            throw new \InvalidArgumentException('Unsupported type to render log. Expected a Log, got a '.(is_object($value) ? get_class($value) : gettype($value)));
        }

        return $this->renderBlock($env, 'log', array(
            'log'      => $value,
            'query_url' => $options['query_url'],
            'per_page' => 20
        ));
    }

    public function renderCommitAttributes(\Twig_Environment $env, $value, array $options = array())
    {
        if (!$value instanceof Commit) {
            throw new \InvalidArgumentException('Unsupported type to render log. Expected a Commit, got a '.(is_object($value) ? get_class($value) : gettype($value)));
        }

        $attrs = array(
            'data-hash' => $value->getHash(),
            'data-parents' => implode(' ', $value->getParentHashes())
        );

        $result = array();
        foreach ($attrs as $key => $val) {
            $result[] = $key.'="'.htmlspecialchars($val, ENT_QUOTES).'"';
        }

        return implode(" ", $result);
    }

    public function renderLogRows(\Twig_Environment $env, $value, array $options = array())
    {
        if (!$value instanceof Log) {
            throw new \InvalidArgumentException('Unsupported type to render log. Expected a Log, got a '.(is_object($value) ? get_class($value) : gettype($value)));
        }

        return $this->renderBlock($env, 'log_rows', array(
            'log'      => $value
        ));
    }

    public function renderAuthor(\Twig_Environment $env, $value, array $options = array())
    {
        $options = array_merge(array(
            'size' => 15
        ), $options);

        if (!$value instanceof Commit) {
            throw new \InvalidArgumentException('Unsupported type to render author. Expected a Commit, got a '.(is_object($value) ? get_class($value) : gettype($value)));
        }

        return $this->renderBlock($env, 'author', array(
            'name'      => $value->getAuthorName(),
            'size'      => $options['size'],
            'email'     => $value->getAuthorEmail(),
            'email_md5' => md5($value->getAuthorEmail())
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
