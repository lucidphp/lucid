<?php

/*
 * This File is part of the Lucid\Module\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template;

use Lucid\Module\Template\Loader\LoaderInterface;
use Lucid\Module\Template\Resource\ResourceInterface;

/**
 * @class AbstractEngine
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
abstract class AbstractEngine implements EngineInterface
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
     * loaded
     *
     * @var array
     */
    private $loaded = [];

    /**
     * Constructor.
     *
     * @param LoaderInterface $loader
     * @param TemplateIdentity $identity
     */
    public function __construct(LoaderInterface $loader, TemplateIdentityInterface $identity = null)
    {
        $this->loader = $loader;
        $this->identity = $identity ?: new TemplateIdentity;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($type)
    {
        return $this->getType() === $type;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($template)
    {
        try {
            return (bool)$this->load($template);
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
    protected function load($template)
    {
        if (!$this->isLoaded($template = $this->getIdentity()->identify($template))) {
            $this->setLoaded($template, $this->getLoader()->load($template));
        }

        return $this->getLoaded($template);
    }

    /**
     * isLoaded
     *
     * @param TemplateInterface $template
     *
     * @return boolean
     */
    protected function isLoaded(TemplateInterface $template)
    {
        return isset($this->loaded[$template->getName()]);
    }

    /**
     * getLoaded
     *
     * @param TemplateInterface $template
     *
     * @return ResourceInterface
     */
    protected function getLoaded(TemplateInterface $template)
    {
        return $this->loaded[$template->getName()];
    }

    /**
     * setLoaded
     *
     * @param TemplateInterface $template
     *
     * @return ResourceInterface
     */
    protected function setLoaded(TemplateInterface $template, ResourceInterface $resource)
    {
        $this->loaded[$template->getName()] = $resource;
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
