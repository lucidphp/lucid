<?php

/*
 * This File is part of the Lucid\Adapter\Mustache package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Adapter\Mustache;

use Mustache_Engine as Mustache;
use Lucid\Template\EngineInterface;
use Lucid\Template\DisplayInterface;
use Lucid\Template\TemplateIdentityInterface;
use Lucid\Template\TemplateIdentity;
use Lucid\Template\Exception\LoaderException;

/**
 * @class MustacheEngine
 *
 * @package Lucid\Adapter\Mustache
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MustacheEngine implements EngineInterface, DisplayInterface
{
    /**
     * types
     *
     * @var array
     */
    protected $types;

    /**
     * mustache
     *
     * @var \Mustache_Engine
     */
    private $mustache;

    /**
     * identity
     *
     * @var TemplateIdentityInterface
     */
    private $identity;

    /**
     * Conostructor.
     *
     * @param \Mustache_Engine $mustache
     * @param TemplateIdentityInterface $identity
     */
    public function __construct(Mustache $mustache, TemplateIdentityInterface $identity = null, $type = 'mustache')
    {
        $this->types = [];
        $this->mustache = $mustache;
        $this->identity = $identity ?: new TemplateIdentity;
        $this->addSupportedType($type);
    }

    /**
     * getMustache
     *
     * @return \Mustache_Engine
     */
    public function getMustache()
    {
        return $this->mustache;
    }

    /**
     * getIdentity
     *
     * @return TemplateIdentityInterface
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * addSupportedType
     *
     * @param mixed $type
     *
     * @return void
     */
    public function addSupportedType($type)
    {
        $this->addSupportedTypes((array)$type);
    }

    /**
     * addSupportedTypes
     *
     * @param array $types
     *
     * @return void
     */
    public function addSupportedTypes(array $types)
    {
        $this->types = array_unique(array_merge($this->types, $types));
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
        echo $this->render($template, $parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($template)
    {
        if ($template instanceof \Mustache_Template) {
            return true;
        }

        try {
            return (bool)$this->loadTemplate($template);
        } catch (LoaderException $e) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($template)
    {
        if ($template instanceof \Mustache_Template) {
            return true;
        }

        return in_array($this->getIdentity()->identify($template)->getType(), $this->types);
    }

    /**
     * loadTemplate
     *
     * @param mixed $template
     *
     * @return \Mustache_Template;
     */
    protected function loadTemplate($template)
    {
        if ($template instanceof \Mustache_Template) {
            return $template;
        }

        try {
            return $this->getMustache()->loadTemplate((string)$template);
        } catch (\Mustache_Exception_UnknownTemplateException $e) {
            throw new LoaderException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
