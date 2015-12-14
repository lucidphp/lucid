<?php

/*
 * This File is part of the Lucid\DI package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\DI\Reference;

/**
 * @class CallerReference
 *
 * @package Lucid\DI
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Caller implements CallerInterface
{
    /** @var ServerInterface */
    private $service;

    /** @var string */
    private $method;

    /** @var array */
    private $arguments;

    /**
     * Constructor.
     *
     * @param ServiceReferenceInterface $service
     * @param string $method
     * @param array $arguments
     */
    public function __construct(ServiceInterface $service, $method, array $arguments = [])
    {
        $this->service   = $service;
        $this->method    = (string)$method;
        $this->arguments = $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * {@inheritdoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
