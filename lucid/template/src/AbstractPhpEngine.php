<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template;

use Lucid\Template\Loader\LoaderInterface;
use Lucid\Template\Resource\ResourceInterface;

/**
 * @class AbstractEngine
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractPhpEngine implements EngineInterface, DisplayInterface, PhpRenderInterface
{
    /**
     * loader
     *
     * @var LoaderInterface
     */
    private $loader;

    /**
     * identity
     *
     * @var TemplateIdentityInterface
     */
    private $identity;

    /**
     * itentities
     *
     * @var array
     */
    private $itentities = [];

    /**
     * loaded
     *
     * @var array
     */
    private $loaded = [];

    /**
     * types
     *
     * @var mixed
     */
    private $types = [];

    /**
     * view
     *
     * @var mixed
     */
    private $view;

    /**
     * Constructor.
     *
     * @param LoaderInterface $loader
     * @param TemplateIdentity $identity
     */
    public function __construct(LoaderInterface $loader, IdentityParserInterface $parser = null)
    {
        $this->loader = $loader;
        $this->identity = $parser ?: new IdentityParser;
    }

    /**
     * {@inheritdoc}
     */
    public function setManager(ViewManagerInterface $view)
    {
        $this->view = $view;
    }

    /**
     * {@inheritdoc}
     */
    public function getManager()
    {
        return $this->view;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($template)
    {
        return in_array($this->findIdentity($template)->getType(), $this->getTypes());
    }

    /**
     * addType
     *
     * @param mixed $type
     *
     * @return void
     */
    public function addType($type)
    {
        $this->types[] = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($template)
    {
        try {
            return (bool)$this->loadTemplate($template);
        } catch (\Exception $e) {
        }

        return false;
    }

    /**
     * Try to load the given template
     *
     * @param mixed $template
     *
     * @return void
     */
    public function loadTemplate($template)
    {
        if (!$this->isLoaded($identity = $this->findIdentity($template))) {
            if (!$this->supports($identity)) {
                throw new \InvalidArgumentException(sprintf('Unsupported template "%s".', $identity->getName()));
            }

            $this->setLoaded($identity, $this->getLoader()->load($identity));
        }

        return $this->getLoaded($identity);
    }

    abstract protected function getParameters($template, array $parameters);

    /**
     * isLoaded
     *
     * @param TemplateInterface $template
     *
     * @return boolean
     */
    protected function isLoaded(IdentityInterface $template)
    {
        return isset($this->loaded[$template->getName()]);
    }

    /**
     * getLoaded
     *
     * @param TemplateInterface $template
     *
     * @return TemplateInterface
     */
    protected function getLoaded(IdentityInterface $identity)
    {
        return $this->loaded[$identity->getName()];
    }

    /**
     * setLoaded
     *
     * @param TemplateInterface $template
     *
     * @return void
     */
    protected function setLoaded(IdentityInterface $identity, ResourceInterface $resource)
    {
        $this->loaded[$identity->getName()] = $resource;
    }

    /**
     * getLoader
     *
     * @return void
     */
    protected function getLoader()
    {
        return $this->loader;
    }

    /**
     * findIdentity
     *
     * @param mixed $template
     *
     * @return TemplateInterface
     */
    protected function findIdentity($template)
    {
        if (!isset($this->identities[(string)$template])) {
            $this->identities[(string)$template] = $this->getIdentity()->identify($template);
        }

        return $this->identities[(string)$template];
    }

    /**
     * getTypes
     *
     * @return array
     */
    protected function getTypes()
    {
        return $this->types;
    }

    /**
     * getIdentity
     *
     *
     * @return void
     */
    protected function getIdentity()
    {
        return $this->identity;
    }
}
