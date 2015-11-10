<?php

/*
 * This File is part of the Lucid\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Template;

/**
 * @interface EngineInterface
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface EngineInterface extends RenderInterface
{
    /**
     * supports
     *
     * @param string $type
     *
     * @return boolean
     */
    public function supports($template);

    /**
     * exists
     *
     * @param mixed $template
     *
     * @return void
     */
    public function exists($template);
}
