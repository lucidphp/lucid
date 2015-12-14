<?php

/*
 * This File is part of the Lucid\Http\Infuse package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Http\Infuse;

use Lucid\Http\Core\DispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @class MiddlewareInterface
 *
 * @package Lucid\Http\Infuse
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface MiddlewareInterface extends DispatcherInterface
{
    /** @var int */
    const PRIORITY_NORMAL = 0;

    /** @var int */
    const PRIORITY_HIGH = 1000;

    /** @var int */
    const PRIORITY_LOW = -1000;

    /**
     * Set the main http dispatcher.
     *
     * @param DispatcherInterface $kernel
     *
     * @return void
     */
    public function setDispatcher(DispatcherInterface $dispatcher);

    /**
     * Get the main http dispatcher.
     *
     * @return DispatcherInterface
     */
    public function getDispatcher();

    /**
     * Get the priority
     *
     * @return int
     */
    public function getPriority();
}
