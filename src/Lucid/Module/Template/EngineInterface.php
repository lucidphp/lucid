<?php

/*
 * This File is part of the Lucid\Module\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template;

/**
 * @interface EngineInterface
 *
 * @package Lucid\Module\Template
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
