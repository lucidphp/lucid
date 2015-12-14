<?php

/*
 * This File is part of the lucid/http-infuse package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Infuse;

use Lucid\Http\Core\DispatcherInterface;
use Lucid\Http\Core\CompleteRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @class Stack
 *
 * @package lucid/http-infuse
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
class Stack implements DispatcherInterface, CompleteRequestInterface
{
    /** @var DispatcherInterface */
    private $dispatcher;

    /**
     * Constructor.
     *
     * @param DispatcherInterface $dispatcher
     */
    public function __construct(DispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    final public function dispatch(ServerRequestInterface $request, $type = DispatcherInterface::T_MAIN, $catch = true)
    {
        return $this->getDispatcher()->handle($request, $type, $catch);
    }

    /**
     * {@inheritdoc}
     */
    public function complete(ServerRequestInterface $request, ResponseInterface $response)
    {
        if (($dispatcher = $this->getDispatcher()) instanceof CompleteRequestInterface) {
            $dispatcher->complete($request, $response);
        } elseif ($dispatcher->getDispatcher() instanceof CompleteRequestInterface) {
            $dispatcher->getDispatcher()->complete($request, $response);
        }
    }

    /**
     * getDispatcher
     *
     * @return DispatcherInterface
     */
    private function getDispatcher()
    {
        return $this->dispatcher;
    }
}
