<?php

/*
 * This File is part of the Lucid\Module\Template package
 *
 * (c) iwyg <mail@thomas-appel.com>
 *
 * For full copyright and license information, please refer to the LICENSE file
 * that was distributed with this package.
 */

namespace Lucid\Module\Template\Data;

/**
 * @interface TemplateDataInterface
 *
 * @package Lucid\Module\Template
 * @version $Id$
 * @author iwyg <mail@thomas-appel.com>
 */
interface TemplateDataInterface
{
    /**
     * addData
     *
     * @param array $data
     *
     * @return void
     */
    public function add($key, $value);

    /**
     * setData
     *
     * @param array $data
     *
     * @return void
     */
    public function set(array $data);

    /**
     * replace
     *
     * @param array $data
     *
     * @return void
     */
    public function replace(array $data);

    /**
     * getData
     *
     * @return array
     */
    public function all(array $parameters = []);
}
