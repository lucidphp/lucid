<?php

/*
 * This File is part of the Lucid\Template\Extension package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template\Extension;

use Lucid\Template\EngineInterface;

/**
 * @class ExtensionInterface
 *
 * @package Lucid\Template\Extension
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface ExtensionInterface
{
    /**
     * Register functions.
     *
     * @param array $functions
     *
     * @return array
     */
    public function functions();

    /**
     * Sets the template engine.
     *
     * @param EngineInterface $engine
     *
     * @return void
     */
    public function setEngine(EngineInterface $engine);

    /**
     * Get the template engine.
     *
     * @return EngineInterface
     */
    public function getEngine();
}
