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

use Lucid\Module\Routing\Matcher\MatchContextInterface;

/**
 * @class HandlerDispatcherInterface
 *
 * @package Lucid\Module\Routing\Handler
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface HandlerDispatcherInterface
{
    /**
     * Delegates a matchcontext to a handler.
     *
     * @param MatchContextInterface $context
     *
     * @return mixed
     */
    public function dispatchHandler(MatchContextInterface $context);
}
