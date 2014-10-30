<?php

/*
 * This File is part of the Lucid\Module\Routing\Matcher package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Matcher;

/**
 * @class MatchContext
 *
 * @package Lucid\Module\Routing\Matcher
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class MatchContext implements MatchContextInterface
{
    private $type;
    private $name;
    private $path;
    private $handler;
    private $parameters;

    /**
     * Constructor.
     *
     * @param mixed $name
     * @param mixed $url
     * @param mixed $handler
     * @param array $params
     */
    public function __construct($type, $name, $url, $handler, array $params = [])
    {
        $this->type       = $type;
        $this->name       = $name;
        $this->path       = $url;
        $this->handler    = $handler;
        $this->parameters = $params;
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
    public function getParameters()
    {
        return $this->parameters;
    }
}
