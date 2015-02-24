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
 * @interface PhpEngineInterface
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface PhpRenderInterface
{
    /**
     * extend
     *
     * @param mixed $template
     *
     * @return void
     */
    public function extend($template);

    /**
     * insert
     *
     * @param mixed $template
     * @param array $replacement
     *
     * @return void
     */
    public function insert($template, array $vars = []);

    /**
     * escape
     *
     * @param mixed $string
     *
     * @return void
     */
    public function escape($string);

    /**
     * section
     *
     * @param mixed $template
     *
     * @return void
     */
    public function section($template);

    /**
     * endsection
     *
     * @return void
     */
    public function endsection();

    /**
     * func
     *
     * @return void
     */
    public function func();
}
