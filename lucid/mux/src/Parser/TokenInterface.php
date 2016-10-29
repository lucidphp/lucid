<?php declare(strict_types=1);

/*
 * This File is part of the Lucid\Mux package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Mux\Parser;

/**
 * @interface TokenInterface
 *
 * @package Lucid\Routing
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface TokenInterface
{
    /**
     * @return string
     */
    public function __toString() : string;

    /**
     * @return \Lucid\Mux\Parser\TokenInterface|null
     */
    public function prev() : ?self;

    /**
     * @return \Lucid\Mux\Parser\TokenInterface|null
     */
    public function next() : ?self;

    /**
     * @return mixed
     */
    public function value();
}
