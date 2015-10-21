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
 * @interface DisplayEngineInterface
 *
 * @package Lucid\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface DisplayInterface
{
    /**
     * Should directly output the contents of a rendered template.
     *
     * @param mixed $template
     * @param array $parameters
     *
     * @return void
     */
    public function display($template, array $parameters = []);
}
