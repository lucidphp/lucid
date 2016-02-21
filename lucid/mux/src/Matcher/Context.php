<?php

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Matcher;

use Lucid\Mux\Request\ContextInterface as RequestContext;

/**
 * @class Context
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Context implements ContextInterface
{
    /** @var int */
    private $type;

    /** @var string */
    private $name;

    /** @var RequestContext */
    private $request;

    /** @var mixed */
    private $handler;

    /** @var array */
    private $vars;

    /**
     * Constructor.
     *
     * @param int $type
     * @param string $name
     * @param string $url
     * @param mixed $handler
     * @param array $vars
     */
    public function __construct($type, $name, RequestContext $request, $handler, array $vars = [])
    {
        $this->type    = $type;
        $this->name    = $name;
        $this->request = $request;
        $this->handler = $handler;
        $this->vars    = $vars;
    }

    /**
     * {@inheritdoc}
     */
    public function isMatch()
    {
        return RequestMatcherInterface::MATCH === $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function isHostMissmatch()
    {
        return RequestMatcherInterface::NOMATCH_HOST === $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function isMethodMissmatch()
    {
        return RequestMatcherInterface::NOMATCH_METHOD === $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function isSchemeMissMatch()
    {
        return RequestMatcherInterface::NOMATCH_SCHEME === $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->request->getPath();
    }

    /**
     * {@inheritdoc}
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * {@inheritdoc}
     */
    public function getVars()
    {
        return $this->vars;
    }
}
