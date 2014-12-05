<?php

/*
 * This File is part of the Lucid\Module\Routing\Handler package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Routing\Handler;

/**
 * @class HandlerParser
 *
 * @package Lucid\Module\Routing\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class HandlerParser implements HandlerParserInterface
{
    /**
     * services
     *
     * @var array
     */
    protected $services;

    /**
     * Constructor.
     *
     * @param array $services
     */
    public function __construct(array $services = [])
    {
        $this->services = $services;
    }

    /**
     * parse
     *
     * @param mixed $handler
     *
     * @return HandlerReflector
     */
    final public function parse($handler)
    {
        if (!is_callable($callable = $this->findHandler($handler))) {
            throw new \RuntimeException('No routing handler could be found.');
        };

        return new HandlerReflector($callable);
    }

    /**
     * findHandler
     *
     * @param string $handler
     *
     * @return callable
     */
    protected function findHandler($handler)
    {
        if (is_callable($handler)) {
            return $handler;
        }

        list ($handler, $method) = $this->listHandlerClassAndMethod($handler);

        if ($service = $this->getService($handler)) {
            return [$service, $method];
        }

        if (class_exists($handler)) {
            try {
                return [new $handler, $method];
            } catch (\Exception $e) {
            }
        }

        return [$handler, $method];
    }

    /**
     * listHandlerClassAndMethod
     *
     * @param string $handler
     *
     * @return array
     */
    protected function listHandlerClassAndMethod($handler)
    {
        return array_pad(explode('@', $handler), 2, null);
    }

    /**
     * getService
     *
     * @param string $id
     *
     * @return void
     */
    protected function getService($id)
    {
        return isset($this->services[$id]) ? $this->services[$id] : null;
    }
}
