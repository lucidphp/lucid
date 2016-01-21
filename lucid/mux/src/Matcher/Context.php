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

/**
 * @class Context
 *
 * @package Lucid\Mux
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Context implements ContextInterface
{
    /**
     * type
     *
     * @var int
     */
    private $type;

    /**
     * name
     *
     * @var string
     */
    private $name;

    /**
     * path
     *
     * @var string
     */
    private $path;

    /**
     * handler
     *
     * @var mixed
     */
    private $handler;

    /**
     * parameters
     *
     * @var array
     */
    private $vars;

    /**
     * Constructor.
     *
     * @param mixed $name
     * @param mixed $url
     * @param mixed $handler
     * @param array $vars
     */
    public function __construct($type, $name, $url, $handler, array $vars = [])
    {
        $this->type    = $type;
        $this->name    = $name;
        $this->path    = $url;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
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
