<?php

/*
 * This File is part of the Lucid\Adapter\Twig package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Twig;

use Twig_Environment;
use Lucid\Module\Template\EngineInterface;
use Lucid\Module\Template\DisplayInterface;
use Lucid\Module\Template\ViewManagerInterface;
use Lucid\Module\Template\IdentityParser;
use Lucid\Module\Template\IdentityParserInterface;
use Lucid\Module\Template\ViewAwareInterface;
use Lucid\Module\Template\Exception\LoaderException;

/**
 * @class TwigEngine
 *
 * @package Lucid\Adapter\Twig
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class TwigEngine implements EngineInterface, DisplayInterface, ViewAwareInterface
{
    private $view;
    private $twig;
    private $itentity;

    /**
     * Constructor.
     *
     * @param \Twig_Environment $twig
     * @param TemplateIdentityInterface $identity
     */
    public function __construct(Twig_Environment $twig, IdentityParserInterface $parser = null)
    {
        $this->twig = $twig;
        $this->identity = $parser ?: new IdentityParser;

        $twig->setBaseTemplateClass(__NAMESPACE__.'\TwigTemplate');
    }

    /**
     * setView
     *
     * @param ViewManagerInterface $view
     *
     * @return void
     */
    public function setManager(ViewManagerInterface $view)
    {
        if ($this->twig instanceof ViewAwareInterface) {
            $this->view = $view;
            $this->twig->setManager($view);
        }
    }

    /**
     * setView
     *
     * @param ViewManagerInterface $view
     *
     * @return void
     */
    public function getManager()
    {
        return $this->view;
    }

    /**
     * {@inheritdoc}
     */
    public function render($template, array $parameters = [])
    {
        return $this->loadTemplate($template)->render($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function display($template, array $parameters = [])
    {
        $this->loadTemplate($template)->display($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($template)
    {
        if ($template instanceof \Twig_Template) {
            return true;
        }

        if (($loader = $this->getTwig()->getLoader()) instanceof \Twig_ExistsLoaderInterface) {
            return $loader->exists($template);
        }

        try {
            return (bool)$loader->getSource();
        } catch (\Twig_Error_Loader $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($template)
    {
        if ($template instanceof \Twig_Template) {
            return true;
        }

        return 'twig' === $this->identity->identify($template)->getType();
    }

    /**
     * Get the Twig instance object.
     *
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * Load the template
     *
     * @param mixed $template
     *
     * @return \Twig_tempalte
     */
    protected function loadTemplate($template)
    {
        if ($template instanceof \Twig_Template) {
            return $template;
        }

        try {
            return $this->getTwig()->loadTemplate($template);
        } catch (\Twig_Error_Loader $e) {
            throw new LoaderException($e->getMessage(), $e->getCode(), $e);
        }

        return $template;
    }
}
